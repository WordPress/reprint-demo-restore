<?php return array(
    'root' => array(
        'name' => 'wp-php-toolkit/reprint-server-plugin',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'wp-php-toolkit/reprint-exporter' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => 'dev-main',
            ),
        ),
        'wp-php-toolkit/reprint-server' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'dcb35e0d1c3378228d6722650c37dda7cd0599e7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../wp-php-toolkit/reprint-server',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wp-php-toolkit/reprint-server-plugin' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
