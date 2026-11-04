# Restore the Reprint Field Notes demo

This is a Reprint snapshot of [reprint-demo.wordpress.net](https://reprint-demo.wordpress.net/), taken on November 4, 2026. It uses Reprint's normal layout: `files/` contains the pulled site files, and `state/` contains the SQL dump and the metadata Reprint needs to restore it. No custom importer is needed.

This public snapshot contains the published site, including its styles, navigation, and photos. It omits revisions, live account credentials, the hosted `wp-config.php`, and the demo's private deployment settings. The `wp-config.php` under `files/` is a local stub. Keep production backups separate.

## Restore and check a local copy

You need PHP CLI with SQLite and GD. Run this from the repository root on macOS or Linux. The commands use a scratch directory so the checked-in snapshot stays unchanged. They download the client from WordPress.org but do not connect to the live demo.

```sh
(
  set -eu
  work=$(mktemp -d)
  cp -R files state "$work/"
  cd "$work"

  curl -fL https://plugins.svn.wordpress.org/reprint/downloads/0.11.6/reprint.phar -o reprint.phar
  source_url=https://reprint-demo.wordpress.net
  php reprint.phar db-apply "$source_url" --state-dir=./state --fs-root=./files \
    --target-engine=sqlite --new-site-url=http://localhost:8881
  php reprint.phar apply-runtime "$source_url" --state-dir=./state --fs-root=./files \
    --runtime=php-builtin --output-dir=./runtime --port=8881
  bash runtime/start.sh
)
```

Open <http://localhost:8881/>. Check the homepage, Journal, About, a story, and a photo. Navigation should stay on the local copy. The download command should point to WordPress.org. Press Ctrl+C to stop the server. If port 8881 is busy, use another port in both commands.

## Recover the hosted demo

1. **Check for a host backup first.** If the hosting team has a known-good backup, use its normal restore process. Otherwise, use this snapshot to rebuild on a fresh, non-public staging site. Leave the current live site and its database in place for rollback.
2. **Restore into a fresh staging database.** Give the hosting maintainer this snapshot. The dump uses the `wp_` table prefix and came from MariaDB. Apply it with Reprint's `db-apply` using `--target-engine=mysql`, the staging database connection options, and `--new-site-url` set to the staging URL. The dump replaces WordPress tables and does not carry over a working administrator login. Never apply it to a database you need to keep. Have the host point staging WordPress at the restored database and provision a new maintainer account there.
3. **Restore the media and theme.** Copy the contents of `files/srv/htdocs/wp-content/uploads/` into the staging uploads directory and use the Twenty Twenty-Five theme. Keep the host's WordPress core and configuration. Do not copy the snapshot's local `wp-config.php` onto the host.
4. **Verify before switching the domain.** Check the staging site as above. Ask the hosting team to promote it and make sure the site's URLs use the live domain. Recheck the pages and photos at the live address.
5. **Restore the Reprint copy endpoint separately.** Ask the hosting maintainers for the approved demo export setup. Don't enable anonymous downloads on an ordinary WordPress install. Verify that the demo export includes only public sample data and rejects uploads. Then run the homepage copy command in a fresh directory and open the resulting local copy.

When updating this repo, review the files and SQL before publishing and repeat the local restore. Do not commit raw pulls, credentials, runtime logs, or generated databases.

## Photo credits

The photos come from the [WordPress Photo Directory](https://wordpress.org/photos/) under [CC0](https://creativecommons.org/publicdomain/zero/1.0/). The About page credits each photographer.
