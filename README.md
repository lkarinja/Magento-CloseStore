# Magento-CloseStore
This module opens and closes the store during specific times of the week.
It prevents users from adding or updating items to their cart during these times.
This also prevents users from placing an order during these times.

These times are determined from the crontime found in \etc\crontab.xml
as well as the .php files found in \Cron\

Override open and close times can be added to \OVERRIDE.csv
in the format "YYYY-MM-DD hh:mm:ss" with the first column being
open time and the second column being close time

Variables are stored in \data\ for access (Do not change these files)

Resources Consulted:

https://magento.stackexchange.com/questions/61430/remove-or-stop-item-to-cart-from-observer
https://magento.stackexchange.com/questions/120695/magento2-how-to-remove-all-carts-items-from-controller
http://blog.chapagain.com.np/magento-clear-delete-shopping-cart-items-of-single-or-all-customers/
https://cyrillschumacher.com/magento2-list-of-all-dispatched-events/
https://stackoverflow.com/questions/16057101/how-to-remove-item-from-quote-in-magento
https://magento.stackexchange.com/questions/98869/how-to-trigger-a-minicart-update-after-adding-to-cart
https://magento.stackexchange.com/questions/141314/remove-items-from-cart-in-controller-magento2
https://magento.stackexchange.com/questions/161316/hot-to-get-all-items-in-observer-on-remove-product-action-on-magento-2
https://magento.stackexchange.com/questions/15172/checkout-cart-add-product-complete-get-quote-item
https://magento.stackexchange.com/questions/132389/get-the-existing-product-id-in-magento-2
https://magento.stackexchange.com/questions/92867/removing-item-from-cart-on-observer-method
https://magento.stackexchange.com/questions/101543/magento2-how-to-stop-a-product-from-getting-added-to-cart-programmatically
https://magento.stackexchange.com/questions/138342/preventing-user-checkout-with-observer-on-sales-order-place-before-event
https://magento.stackexchange.com/questions/111137/magento-2-how-to-get-all-items-in-cart
https://stackoverflow.com/questions/34957111/magento-2-event-dispatched-when-a-user-clicks-go-to-checkout

https://magento.stackexchange.com/questions/138355/magento-2-check-condition-on-click-of-proceed-to-checkout
https://www.magestore.com/magento-2-tutorial/use-event-magento-2/
https://secure.php.net/manual/en/function.fgetcsv.php
https://webkul.com/blog/update-cart-adding-product-programmatically-magento2/
http://webkul.com/blog/add-product-cart-magento2/
https://magento.stackexchange.com/questions/100615/magento-2-how-can-refresh-minicart-cache-after-clear-cart-session-and-place-orde/120407#120407
https://github.com/magento/magento2/issues/5377
https://github.com/magento/magento2/issues/4170
https://magento.stackexchange.com/questions/97476/magento-2-path-to-place-footer-phtml-in-custom-theme
https://magento.stackexchange.com/questions/127861/default-xml-in-my-custom-module-isnt-working
https://magento.stackexchange.com/questions/141314/remove-items-from-cart-in-controller-magento2/141334#141334
https://magento.stackexchange.com/questions/135005/magento-2-minicart-cant-remove-items/150017
https://magento.stackexchange.com/questions/110472/update-minicart-after-adding-products-programmaticaly
https://webkul.com/blog/update-item-quantity-minicart-magento2/
https://magento.stackexchange.com/questions/157890/magento-2-remove-mini-cart-item-page-reload
https://magento.stackexchange.com/questions/127203/magento-2-minicart-does-not-clear-items-after-checkout
https://github.com/magento/magento2/pull/5807/files#diff-1

https://www.atwix.com/magento-2/choose-your-perfect-event/
https://magento.stackexchange.com/questions/76515/how-to-show-session-messages-at-front-end-in-magento-2-beta
https://www.extensionsmall.com/blog/display-notification-messages-in-magento-2/
https://magento.stackexchange.com/questions/122365/magento-2-run-function-on-every-page
https://stackoverflow.com/questions/15616489/magento-event-on-any-page-load
https://github.com/benmarks/magento-mirror/blob/1.7.0.2/app/code/core/Mage/Core/Controller/Varien/Action.php#L300
https://magento.stackexchange.com/questions/122365/magento-2-run-function-on-every-page
https://magento.stackexchange.com/questions/131706/magento-2-redirect-user-to-a-specific-page-if-not-logged-in
https://magento.stackexchange.com/questions/154838/magento-2-how-to-get-order-data-in-observer-on-success-page
https://stackoverflow.com/questions/15795849/how-can-i-determine-where-an-event-was-dispatched-from-in-magento
https://firebearstudio.com/blog/magento-2-observers.html
https://github.com/magento/magento2/blob/2.0.13/lib/internal/Magento/Framework/View/Layout/Builder.php#L79
https://github.com/magento/magento2/blob/2.0.13/app/code/Magento/Cms/Helper/Page.php#L157
https://github.com/magento/magento2/blob/2.0.13/app/code/Magento/Theme/Block/Html/Topmenu.php#L83
https://github.com/magento/magento2/blob/2.0.13/lib/internal/Magento/Framework/App/View.php#L217
https://github.com/magento/magento2/blob/2.0.13/lib/internal/Magento/Framework/App/Action/Action.php#L91
https://magento.stackexchange.com/questions/112948/magento-2-how-do-customer-sections-sections-xml-work
http://inchoo.net/magento-2/magento-2-controllers/
https://magento.stackexchange.com/questions/34225/how-do-i-modify-magento-rendered-page-before-its-displayed
https://magento.stackexchange.com/questions/127476/how-to-show-success-message-in-session-magento2/127487
https://github.com/magento/magento2/issues/7427
https://stackoverflow.com/questions/13251078/magento-losing-messages-after-redirect
https://magento.stackexchange.com/questions/112993/magento2-redirection-from-observer
https://magento.stackexchange.com/questions/158378/magento-2-event-observer-exception-not-showing-in-screen
https://drupal.stackexchange.com/questions/166768/remove-duplicate-messages
https://magento.stackexchange.com/questions/83138/how-to-use-messagemanager-to-show-an-error-after-redirect
https://github.com/nexcess/magento-turpentine/issues/55
https://stackoverflow.com/questions/15616489/magento-event-on-any-page-load
https://magento.stackexchange.com/questions/107634/check-if-block-rendered-and-displayed-on-the-current-page
https://magento.stackexchange.com/questions/139837/magento-2-layout-events-controller-action-layout-render-before-vs-layout-rende
https://github.com/magento/magento2/issues/3601

https://serverfault.com/questions/449651/why-is-my-crontab-not-working-and-how-can-i-troubleshoot-it
https://askubuntu.com/questions/23009/why-crontab-scripts-are-not-working
https://www.computerhope.com/unix/ucrontab.htm
https://github.com/magento/magento2/issues/5836
https://unix.stackexchange.com/questions/107371/how-to-turn-off-color-with-ls
http://www.thegeekstuff.com/2012/12/linux-tr-command
https://unix.stackexchange.com/questions/189205/replace-n-with-blank-in-a-file
https://stackoverflow.com/questions/21246901/append-output-of-a-command-to-file-without-newline
https://stackoverflow.com/questions/20355264/how-to-redirect-output-of-multiple-commands-to-one-file
https://superuser.com/questions/28633/checking-what-php-version-im-running-on-linux
https://www.maximehuran.fr/en/create-a-cron-job-with-magento-2/
http://devdocs.magento.com/guides/v2.0/config-guide/cron/custom-cron-ref.html
http://devdocs.magento.com/guides/v2.0/config-guide/cli/config-cli-subcommands-cron.html
https://github.com/magento/magento2/issues/6962
https://secure.php.net/manual/en/function.file-exists.php
https://magento.stackexchange.com/questions/154537/where-do-we-store-persistent-single-variables-in-magento-2-module-settings
https://secure.php.net/manual/en/language.types.integer.php
https://stackoverflow.com/questions/1956554/php-not-returning-value-of-false
https://secure.php.net/manual/en/language.operators.comparison.php
http://devdocs.magento.com/guides/v2.0/comp-mgr/prereq/prereq_cron.html
https://magento.stackexchange.com/questions/32393/schedule-cron-job-every-four-hours
http://www.nncron.ru/help/EN/working/cron-format.htm
http://inchoo.net/magento-2/running-cron-jobs-in-magento-2/
https://www.atwix.com/magento-2/setting-up-cron-jobs/
https://www.mageplaza.com/magento-2-registry-register.html
http://devdocs.magento.com/guides/v2.0/config-guide/cli/config-cli-subcommands-cron.html
https://secure.php.net/manual/en/function.fgetcsv.php
https://magento.stackexchange.com/questions/178108/magento-2-display-notification-on-all-store-pages

https://secure.php.net/manual/en/function.file-exists.php
https://stackoverflow.com/questions/11094776/php-how-to-go-one-level-up-on-dirname-file
https://unix.stackexchange.com/questions/8584/using-the-system-date-time-in-a-cron-script
https://serverfault.com/questions/368009/escaping-characters-in-cron
http://linuxcommand.org/wss0010.php
https://stackoverflow.com/questions/14219092/bash-my-script-bin-bashm-bad-interpreter-no-such-file-or-directory
https://stackoverflow.com/questions/37574458/how-to-run-a-bash-script-via-cron
https://stackoverflow.com/questions/6977568/php-multiple-line-comment-inside-multiple-line-comment
https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
https://www.w3schools.com/php/func_array_push.asp
https://secure.php.net/manual/en/control-structures.foreach.php
https://secure.php.net/manual/en/function.strtotime.php
https://secure.php.net/manual/en/datetime.gettimestamp.php
https://stackoverflow.com/questions/4150435/php-strtotime-last-monday-if-today-is-monday
http://www.nncron.ru/help/EN/working/cron-format.htm
http://devdocs.magento.com/guides/v2.0/config-guide/cron/custom-cron-ref.html
http://www.nncron.ru/help/EN/working/cron-format.htm
https://magento.stackexchange.com/questions/174490/magento-2-how-to-access-the-registry-in-controller-defined-in-template-fiile
https://magento.stackexchange.com/questions/94265/how-to-set-retrieve-and-unset-session-variables-in-magento-2
https://stackoverflow.com/questions/26364740/difference-between-mageregistry-and-session-in-magento
https://stackoverflow.com/questions/949779/setting-a-global-variable-in-magento-the-gui-way
https://www.atwix.com/magento-2/working-with-custom-configuration-files/
https://magento.stackexchange.com/questions/117393/choose-session-magento-2
https://stackoverflow.com/questions/2995461/save-php-variables-to-a-text-file
https://secure.php.net/manual/en/function.file-put-contents.php

https://magento.stackexchange.com/questions/87972/magento2-notification-messages
https://github.com/magento/magento2/blob/2.0.13/lib/internal/Magento/Framework/View/Layout/Builder.php#L79
https://www.demacmedia.com/magento-commerce/create-a-magento-2-plugin/
https://www.mageplaza.com/magento-2-module-development/magento-2-plugin-interceptor.html
https://magento.stackexchange.com/questions/124883/magento2-plugin-class-not-working
https://magento.stackexchange.com/questions/84220/plugins-not-called-in-magento-2
https://magento.stackexchange.com/questions/159722/magento-2-plugin-not-called/159725
https://magento.stackexchange.com/questions/150797/magento-2-how-to-override-protected-function
http://brideo.co.uk/magento2/programmatically-add-layout-handles-using-an-observer-in-magento-2/
https://stackoverflow.com/questions/15616489/magento-event-on-any-page-load
https://makandracards.com/magento/10723-messages-and-global-messages-blocks
https://magento.stackexchange.com/questions/34225/how-do-i-modify-magento-rendered-page-before-its-displayed
http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html
https://stackoverflow.com/questions/8798443/best-way-to-destroy-php-object
https://stackoverflow.com/questions/3845051/php-reset-clear-an-object
http://www.stoimen.com/blog/2011/11/14/php-dont-call-the-destructor-explicitly/
https://magento.stackexchange.com/questions/109922/magento-2-how-to-route-the-page-to-catalogsearch-advanced-result-page-with-abc
https://magento.stackexchange.com/questions/135560/where-is-a-template-for-messages-in-magento-2

https://magento.stackexchange.com/questions/101395/refresh-the-cache-programmatically-in-magento-2-at-window-system/131837
