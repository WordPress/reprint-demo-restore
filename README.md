# Restore the Reprint Field Notes demo

Use this kit to rebuild [reprint-demo.wordpress.net](https://reprint-demo.wordpress.net/) if its WordPress site is lost or damaged. It contains the published pages, six stories, block templates, navigation, styles, and photos from November 4, 2026. It does not contain WordPress accounts, a production database dump, or hosting credentials. You can restore the site content even when the live demo is offline.

If the hosting team has a known-good backup, use its normal restore process first. Use this kit when you need to rebuild the site content on a fresh WordPress install.

## 1. Prepare a staging site

Ask the hosting team for a fresh, non-public, single-site WordPress staging install and SSH or WP-CLI access. Leave the live site in place. This kit was tested with WordPress 7.1.4 and Twenty Twenty-Five 1.5. Use a supported WordPress version and install [Twenty Twenty-Five](https://wordpress.org/themes/twentytwentyfive/) if it is missing.

Clone this repo somewhere **outside** the staging web root. From the staging WordPress directory, set `kit` to that clone and check the target:

```sh
kit=/path/to/reprint-demo-restore
test -f "$kit/site.json"
wp option get home
wp eval 'echo wp_get_environment_type(), PHP_EOL;'
```

Continue only if the URL is your staging site and the environment is `staging` (or `local` for a rehearsal). If the host manages the environment setting, ask the hosting team to set it. Do not change the live site's setting to run the importer.

## 2. Restore the content

Run these commands from the staging WordPress directory. `wp site empty` deletes posts, comments, terms, and links from its target. Confirm the prompt only on the **new staging install**, where there is no content to keep. The maintainer login you created when setting up WordPress stays in place.

```sh
wp theme activate twentytwentyfive
wp site empty
```

After both commands succeed, import the site:

```sh
(
  set -eu
  author_id=$(wp user create field-notes field-notes@example.invalid \
    --role=author --display_name='Field Notes' --porcelain)
  wp eval-file "$kit/restore.php" "$author_id"

  uploads=$(wp eval 'echo wp_get_upload_dir()["basedir"];')
  test -n "$uploads"
  mkdir -p "$uploads"
  cp -R "$kit/uploads/." "$uploads/"
  wp rewrite flush
)
```

The separate author account supplies the “Field Notes” byline; use your own maintainer account to sign in. The importer keeps the original post IDs so blocks still find the right menus and photos. It refuses a nonempty site or the live demo URL. If the import fails partway through, start again on a fresh staging install.

## 3. Verify and switch over

- Open the staging homepage, Journal, About, and one story. Check that navigation stays on staging and the photos load. The homepage download command should point to `plugins.svn.wordpress.org`.
- Keep a rollback copy of the current live site. Have the hosting team move the demo domain to the verified install, or promote staging using the host's normal process. Do not overwrite the host's WordPress configuration. If any content links still point to staging, ask the hosting team to run a WordPress-aware URL replacement for that staging URL. Check the pages again at the live address.
- Restore the **Reprint copy endpoint** separately with the maintainers who have access to its approved host configuration. This kit restores the website content; it does not enable anonymous downloads. Do not enable a public exporter on an ordinary WordPress install. Before reopening the demo, verify that its export includes only public sample data and rejects uploads. Then run the homepage's copy command in a new directory and open the resulting local copy.

If you refresh this kit, include only reviewed public content and media. Restore into fresh staging and repeat these checks before publishing an update. Keep production backups and host settings out of this repo.

## Photo credits

The photos come from the [WordPress Photo Directory](https://wordpress.org/photos/) under [CC0](https://creativecommons.org/publicdomain/zero/1.0/). The restored About page credits each photographer and links to the original photo.
