#!/usr/bin/env bash
#
# Build the plugin zip that users install and that goes to WordPress.org SVN.
#
# Usage:  bash bin/build-zip.sh
# Output: build/webcodingplace-post-carousel-for-elementor-{version}.zip
#
# The zip holds the committed files only (uncommitted changes are left out),
# minus everything listed in .distignore: docs, tests, dev tools, the
# WordPress.org assets folder and so on. Needs git and nothing else.

set -euo pipefail

SLUG="webcodingplace-post-carousel-for-elementor"
cd "$(dirname "$0")/.."

VERSION="$(sed -n 's/^ \* Version: *//p' "$SLUG.php" | tr -d '[:space:]')"
STABLE="$(sed -n 's/^Stable tag: *//p' readme.txt | tr -d '[:space:]')"
CONSTANT="$(sed -n "s/.*define( 'DPCE_VERSION', '\([^']*\)' );.*/\1/p" "$SLUG.php")"

if [ "$VERSION" != "$STABLE" ] || [ "$VERSION" != "$CONSTANT" ]; then
	echo "Version mismatch: header $VERSION, Stable tag $STABLE, DPCE_VERSION $CONSTANT." >&2
	echo "Set all three to the same number before building." >&2
	exit 1
fi

if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
	echo "Note: you have uncommitted changes. The zip only contains committed files." >&2
fi

# Turn each .distignore line into a git "exclude" pathspec.
EXCLUDES=()
while IFS= read -r line || [ -n "$line" ]; do
	line="${line%$'\r'}"
	case "$line" in '' | '#'*) continue ;; esac
	if [ "${line:0:1}" = "/" ]; then
		EXCLUDES+=(":(exclude,top)${line:1}")
	else
		EXCLUDES+=(":(exclude,glob)**/$line")
	fi
done < .distignore

mkdir -p build
OUT="build/$SLUG-$VERSION.zip"
rm -f "$OUT"

git archive --format=zip --prefix="$SLUG/" -o "$OUT" HEAD -- . "${EXCLUDES[@]}"

echo "Built $OUT"
echo "Files: $(unzip -l "$OUT" | tail -1 | awk '{print $2}'), size: $(du -h "$OUT" | cut -f1)"
