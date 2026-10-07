# Releasing a new version

How to turn this repository into a release on WordPress.org. Written for 2.0, and the same for every later version.

## Why the repository has so many files, and what users get

Most files in the repository are for development only: docs, coding standards, static analysis, CI, build scripts and the WordPress.org page assets. `.distignore` lists them, and they never go into the plugin users install.

The release zip contains only what the plugin needs to run, about 220 files and 400 KB:

```
webcodingplace-post-carousel-for-elementor/
├── webcodingplace-post-carousel-for-elementor.php
├── uninstall.php
├── readme.txt
├── index.php
├── assets/       CSS, JavaScript, Slick, template thumbnails
├── includes/     PHP classes
├── languages/    translation template (.pot)
├── templates/    the 52 card templates
└── widgets/      the Elementor widget
```

## 1. Build the zip

Either way gives the same file.

**On your computer** (needs git; on Windows use Git Bash):

```
bash bin/build-zip.sh
```

The zip lands in `build/webcodingplace-post-carousel-for-elementor-2.0.zip`. The script refuses to build when the version in the plugin header, `DPCE_VERSION` and `Stable tag` differ. It packs the last commit, so commit your changes first.

**On GitHub, no tools needed:** open the repository's **Actions** tab, pick the latest **CI** run on `main` and download **plugin-zip** at the bottom of the run page. GitHub wraps it in one more zip; unzip that once to get the plugin zip. You can start a run yourself with **Run workflow**. The same job runs the official Plugin Check against this exact zip, so a green run means the zip passed.

Install the zip on a test site once (**Plugins > Add New > Upload Plugin**) before you release.

## 2. Where the WordPress.org images go

The banner, icon and screenshots are **not** part of the plugin. They live in the `assets` folder of the plugin's SVN repository, next to `trunk` and `tags`:

```
SVN repository
├── assets/              images for the WordPress.org page
│   ├── banner-772x250.png
│   ├── banner-1544x500.png
│   ├── icon-128x128.png
│   ├── icon-256x256.png
│   ├── screenshot-1.png  ... screenshot-9.png
│   └── blueprints/
│       └── blueprint.json   (Live Preview)
├── trunk/               the plugin files (the unzipped zip)
└── tags/
    ├── 1.4/
    └── 2.0/             a copy of trunk for this release
```

Keep your source copies in this repository's `.wordpress-org/` folder with the same names. It is already left out of the zip, and it keeps everything in one place. `docs/ASSETS-BRIEF.md` describes each image. Screenshot numbers must match the captions in `readme.txt`.

## 3. Publish with SVN

Your WordPress.org username and password are needed for the commit. The SVN password is set under **Account & Security** on your WordPress.org profile.

**Command line** (macOS, Linux, Git Bash on Windows with SVN installed):

```
# Once: get a working copy of the SVN repository.
svn checkout https://plugins.svn.wordpress.org/webcodingplace-post-carousel-for-elementor wcp-svn
cd wcp-svn
svn update

# trunk: replace its files with the new release.
rm -rf trunk/*
unzip -q /path/to/webcodingplace-post-carousel-for-elementor-2.0.zip -d /tmp/wcp
cp -R /tmp/wcp/webcodingplace-post-carousel-for-elementor/. trunk/
svn add --force trunk
svn status trunk | grep '^!' | awk '{print $2}' | xargs -r svn rm   # files that no longer exist

# tags: copy trunk as the new version.
svn cp trunk tags/2.0

# assets: images and the Live Preview blueprint.
cp /path/to/repo/.wordpress-org/*.png assets/
mkdir -p assets/blueprints
cp /path/to/repo/.wordpress-org/blueprints/blueprint.json assets/blueprints/
svn add --force assets
svn propset svn:mime-type image/png assets/*.png    # so browsers show them instead of downloading
svn propset svn:mime-type image/jpeg assets/*.jpg 2>/dev/null || true

# Check, then publish.
svn status
svn commit -m "Release 2.0" --username your-wporg-username
```

Do not copy `.wordpress-org/blueprints/images/` to SVN. The Live Preview downloads those demo images from GitHub.

**Windows with TortoiseSVN:** right click > **SVN Checkout** with the URL above. Copy the unzipped plugin files into `trunk` (replace all), right click `trunk` > **TortoiseSVN > Add** to add new files. Right click `trunk` > **TortoiseSVN > Branch/tag**, to path `/tags/2.0`. Copy the images into `assets`, add them, then **SVN Commit**.

WordPress.org publishes the version named in `trunk/readme.txt` as `Stable tag` (2.0), using the files in `tags/2.0`. Sites see the update within about 15 minutes. The page images can take a few hours to refresh because of caching.

## 4. After the commit

1. Turn on the Live Preview: while logged in as the plugin owner, open the plugin's page, switch to **Advanced View** and turn on the preview setting there. Then click **Live Preview** once to check it.
2. Check the plugin page: name, banner, icon, screenshots in the right order, the 2.0 changelog.
3. Update a test site from the dashboard (not by uploading) to see the update the way users will.
4. Tag the release on GitHub too (`v2.0` on `main`) so the code and the release match.

## For the next version

1. Change the version in three places together: `Version:` in the plugin header, `DPCE_VERSION`, and `Stable tag` in `readme.txt`.
2. Add a changelog entry and, for anything users must know, an Upgrade Notice.
3. Regenerate the translation template if strings changed: `wp i18n make-pot . languages/webcodingplace-post-carousel-for-elementor.pot --exclude=vendor,node_modules,tests,docs,bin,build,assets/vendor,.wordpress-org`
4. When a new WordPress or Elementor version comes out, test, then raise `Tested up to` in `readme.txt` or `Elementor tested up to` in the plugin header. A version bump is not needed for this; commit the readme to `trunk` and to the current tag.
5. Build the zip, test it, and repeat step 3 with the new number.
