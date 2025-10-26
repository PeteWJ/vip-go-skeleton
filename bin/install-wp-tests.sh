#!/usr/bin/env bash
# Install WordPress test suite without requiring SVN
# This script downloads the test files via GitHub instead of SVN

set -e

WP_VERSION=${1-latest}
TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$(pwd)/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$(pwd)/tmp/wordpress}

download() {
    if [ `which curl` ]; then
        curl -s -L "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+\-(beta|RC)[0-9]+$ ]]; then
	WP_BRANCH=${WP_VERSION%\-*}
	WP_TESTS_TAG="branches/$WP_BRANCH"
elif [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
	WP_TESTS_TAG="branches/$WP_VERSION"
elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0-9]+ ]]; then
	if [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0] ]]; then
		# version x.x.0 means the first release of the major version, so strip off the .0 and download version x.x
		WP_TESTS_TAG="tags/${WP_VERSION%??}"
	else
		WP_TESTS_TAG="tags/$WP_VERSION"
	fi
elif [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
	WP_TESTS_TAG="trunk"
else
	# http serves a single offer, whereas https serves multiple. we only want one
	download http://api.wordpress.org/core/version-check/1.7/ $TMPDIR/wp-latest.json
	LATEST_VERSION=$(grep -o '"version":"[^"]*' $TMPDIR/wp-latest.json | sed 's/"version":"//' | head -1)
	if [[ -z "$LATEST_VERSION" ]]; then
		echo "Latest WordPress version could not be found"
		exit 1
	fi
	WP_TESTS_TAG="tags/$LATEST_VERSION"
fi

echo "Installing WordPress test suite..."
echo "  - WP version: $WP_VERSION"
echo "  - Test suite dir: $WP_TESTS_DIR"
echo "  - WP core dir: $WP_CORE_DIR"

# Set up WordPress core
mkdir -p $WP_CORE_DIR

if [ ! -f $WP_CORE_DIR/wp-load.php ]; then
	echo "Downloading WordPress..."
	if [ $WP_VERSION == 'latest' ]; then
		ARCHIVE_NAME='latest'
	elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+ ]]; then
		# https serves multiple offers, whereas http serves single.
		download https://wordpress.org/wordpress-${WP_VERSION}.tar.gz $TMPDIR/wordpress.tar.gz
		ARCHIVE_NAME="wordpress-${WP_VERSION}"
	else
		ARCHIVE_NAME='latest'
	fi

	if [ $ARCHIVE_NAME == 'latest' ]; then
		download https://wordpress.org/latest.tar.gz $TMPDIR/wordpress.tar.gz
	fi

	tar --strip-components=1 -zxf $TMPDIR/wordpress.tar.gz -C $WP_CORE_DIR
fi

# Set up testing suite
mkdir -p $WP_TESTS_DIR/includes

echo "Downloading test suite from GitHub (this may take a moment)..."

# Download the entire test suite as a zipball and extract just what we need
WP_TEST_ARCHIVE=$TMPDIR/wp-test-suite.zip
download https://github.com/WordPress/wordpress-develop/archive/trunk.zip $WP_TEST_ARCHIVE

# Extract the test suite files
if command -v unzip > /dev/null; then
	unzip -q -o $WP_TEST_ARCHIVE "*/tests/phpunit/includes/*" -d $TMPDIR
	unzip -q -o $WP_TEST_ARCHIVE "*/tests/phpunit/data/*" -d $TMPDIR
	unzip -q -o $WP_TEST_ARCHIVE "*/wp-tests-config-sample.php" -d $TMPDIR

	# Move files to the correct location
	cp -R $TMPDIR/wordpress-develop-trunk/tests/phpunit/includes/* $WP_TESTS_DIR/includes/
	cp -R $TMPDIR/wordpress-develop-trunk/wp-tests-config-sample.php $WP_TESTS_DIR/wp-tests-config.php

	# Copy data directory if it exists
	if [ -d "$TMPDIR/wordpress-develop-trunk/tests/phpunit/data" ]; then
		mkdir -p $WP_TESTS_DIR/data
		cp -R $TMPDIR/wordpress-develop-trunk/tests/phpunit/data/* $WP_TESTS_DIR/data/
	fi

	# Clean up
	rm -rf $TMPDIR/wordpress-develop-trunk
	rm -f $WP_TEST_ARCHIVE

	echo "Test suite files downloaded and extracted."
else
	echo "Error: unzip command not found. Please install unzip to continue."
	exit 1
fi

# Create wp-content directory for SQLite drop-in
mkdir -p $WP_TESTS_DIR/wp-content

# Update the config file with test database credentials (for MySQL tests)
if [ -f "DB-CONFIG" ]; then
	echo "Using DB-CONFIG for database credentials"
	DB_HOST=$(grep db_host DB-CONFIG | cut -d':' -f2 | xargs)
	DB_NAME=$(grep db_name DB-CONFIG | cut -d':' -f2 | xargs)
	DB_USER=$(grep db_user DB-CONFIG | cut -d':' -f2 | xargs)
	DB_PASS=$(grep db_pass DB-CONFIG | cut -d':' -f2 | xargs)
else
	echo "No DB-CONFIG found, using defaults"
	DB_HOST=${DB_HOST-localhost}
	DB_NAME=${DB_NAME-wordpress_tests}
	DB_USER=${DB_USER-root}
	DB_PASS=${DB_PASS-''}
fi

# Update wp-tests-config.php
sed -i.bak "s/youremptytestdbnamehere/$DB_NAME/" $WP_TESTS_DIR/wp-tests-config.php
sed -i.bak "s/yourusernamehere/$DB_USER/" $WP_TESTS_DIR/wp-tests-config.php
sed -i.bak "s/yourpasswordhere/$DB_PASS/" $WP_TESTS_DIR/wp-tests-config.php
sed -i.bak "s|localhost|${DB_HOST}|" $WP_TESTS_DIR/wp-tests-config.php
sed -i.bak "s|dirname( __FILE__ ) . '/src/'|'$WP_CORE_DIR/'|" $WP_TESTS_DIR/wp-tests-config.php

# Add a note about SQLite configuration (will be set in bootstrap-sqlite.php)
sed -i.bak "/\/\/ \*\* Database settings \*\* \/\//a\\
\\
\\/\\/ SQLite configuration (used when running with bootstrap-sqlite.php)\\
\\/\\/ Uncomment these lines if you want to use SQLite:\\
\\/\\/ define( 'DB_DIR', dirname( __FILE__ ) . '/../sqlite' );\\
\\/\\/ define( 'DB_FILE', 'test.sqlite' );\\
" $WP_TESTS_DIR/wp-tests-config.php

rm $WP_TESTS_DIR/wp-tests-config.php.bak

echo "WordPress test suite installed successfully!"
echo ""
echo "You can now run tests with:"
echo "  composer test          # MySQL/MariaDB"
echo "  composer test:sqlite   # SQLite (no database needed)"
