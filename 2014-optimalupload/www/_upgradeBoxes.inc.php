<?php
$days = array(7, 30, 90, 180, 365);
?>
<?php foreach ($days AS $k => $day): ?>
    <div class="upgradeSection"<?php if (($k + 1) != COUNT($days)) echo ' style="padding-right: 12px;"'; ?>>
        <div class="upgradeContent ui-corner-all">
            <div class="upgradeContentInternal">
                <div class="period">
                    <?php echo $day; ?> <?php echo UCWords(t('days', 'days')); ?>
                </div>
                <div class="clear"></div>
                <div class="premium">
                    <?php echo UCWords(t('premium', 'premium')); ?>
                </div>
                <div class="clear"></div>
                <div class="totalPrice">
                    <?php echo SITE_CONFIG_COST_CURRENCY_SYMBOL; ?><?php echo number_format(str_replace(",", "", constant('SITE_CONFIG_COST_FOR_' . $day . '_DAYS_PREMIUM')), 2); ?>
                </div>
                <div class="clear"></div>
                <div class="pricePerDay">
                    <?php echo SITE_CONFIG_COST_CURRENCY_SYMBOL; ?><?php echo number_format(str_replace(",", "", constant('SITE_CONFIG_COST_FOR_' . $day . '_DAYS_PREMIUM')) / $day, 2); ?> <?php echo UCWords(t('upgrade_boxes_per_day', 'per day')); ?>
                </div>
                <div class="clear"></div>
                <?php
                pluginHelper::outputPaymentLinks($day);
                ?>
                <div class="clear"></div>
                <div class="secure">
                    <img src="<?php echo SITE_IMAGE_PATH; ?>/icon_padlock.gif" width="12" height="12" alt="<?php echo UCWords(t('secure_payment', 'secure payment')); ?>" style="vertical-align:middle;"/>
                    <span style="vertical-align: middle;">&nbsp;<?php echo UCWords(t('safe_and_anonymous', '100% Safe & Anonymous')); ?></span>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="clear"></div>