<div class="wrap">
    <h2>
        <?php _e('CoinIMP Miner Settings', 'coinimp-miner-script-installer'); ?>
        <a class="add-new-h2" target="_blank"
           href="<?php echo esc_url("https://www.coinimp.com"); ?>"> <?php _e('CoinIMP Website', 'coinimp-miner-script-installer'); ?>
        </a>
    </h2>
    <hr/>

    <form name="dofollow" action="options.php" method="post">
        <?php settings_fields('coinimp-miner-script-installer'); ?>
        <h3 class="coinimp-labels" for="coinimp_sitekey">
            <?php _e('Currency', 'coinimp-miner-script-installer'); ?>
        </h3>
        <script nonce="IP6P03N6Lqw56G+jxhWtww==">
            function setSiteKey()
            {
                var siteKeys = <?php echo json_encode(get_option('coinimp_previousSiteKeys')); ?> ;
                var currSiteKey = siteKeys[document.getElementById("coinimp_currentcurrency").selectedIndex];
                if (currSiteKey === undefined || currSiteKey === null)
                    setDefaultSiteKey();
                 else
                    document.getElementById("coinimp_sitekey").value = currSiteKey;
            }
            function setDefaultSiteKey()
            {
                var defaultSiteKeys = <?php echo json_encode(get_option('coinimp_defaultsitekeys')); ?> ;
                document.getElementById("coinimp_sitekey").value= defaultSiteKeys[document.getElementById("coinimp_currentcurrency").selectedIndex];
            }

        </script>
        <select style="width:98%;" rows="1" cols="57" id="coinimp_currentcurrency" name="coinimp_currentcurrency" onchange='setSiteKey()' <?php if (count(get_option('coinimp_currencies')) == 1) echo 'disabled'; ?>>
            <?php
            for ($x = 0; $x <= (count(get_option('coinimp_currencies')) - 1); $x++) {
                echo "<option " . ((get_option('coinimp_currentcurrency') == $x) ? "selected " : "") . "value='$x'>". (get_option('coinimp_currencies')[$x] == 'web' ? 'MINTME' : strtoupper(get_option('coinimp_currencies')[$x])) . "</option>";
            }
            echo "</select>";
            ?>
        <?php
            if (count(get_option('coinimp_currencies')) == 1) {
                echo '<input type="hidden" name="coinimp_currentcurrency" value="0">';
            }
        ?>
        <h3 class="coinimp-labels" for="coinimp_sitekey">
            <?php _e('Site key', 'coinimp-miner-script-installer'); ?>
            <a class="add-new-h2" onclick="setDefaultSiteKey()" href="#">Set default site key</a>
        </h3>
        <textarea style="width:98%;" rows="1" id="coinimp_sitekey" name="coinimp_sitekey"><?php echo esc_html(get_option('coinimp_sitekey')); ?></textarea>
            <br>
        <hr/>

        <h3 class="coinimp-labels" for="coinimp_throttle">
            <?php _e('CPU usage', 'coinimp-miner-script-installer'); ?>
        </h3>

        <select style="width:98%;" rows="1" cols="57" id="coinimp_throttle" name="coinimp_throttle">
            <?php
            for ($x = 100; $x >= 10; $x-=10) {
            $throttle = 1 - ($x/100);
                echo "<option " . (((string) get_option('coinimp_throttle') == (string) $throttle) ? "selected " : "") . "value='$throttle'>".$x . "%</option>";
            }
            ?>
        </select>

        <hr/>

        <h3 class="coinimp-labels">
            <?php _e('Mining Notification Settings', 'coinimp-miner-script-installer'); ?>
        </h3>

        <h4 class="coinimp-labels">
            <?php _e('Notification Method', 'coinimp-miner-script-installer'); ?>
        </h4>

        <input type="radio" name="coinimp_notify"
               value="Never" <?php if (get_option('coinimp_notify') == "Never") echo 'checked'; ?>> Disable<br>
        <input type="radio" name="coinimp_notify"
               value="Floating" <?php if (get_option('coinimp_notify') == "Floating") echo 'checked'; ?>> Floating text box on the bottom right corner<br>
        <input type="radio" name="coinimp_notify"
               value="Footer" <?php if (get_option('coinimp_notify') == "Footer") echo 'checked'; ?>> Fixed footer notification<br>
        <input type="radio" name="coinimp_notify"
               value="Popup" <?php if (get_option('coinimp_notify') == "Popup") echo 'checked'; ?>> Pop-up message upon the user's first visit<br>

        <h4 class="coinimp-labels">
            <?php _e('Notification Message', 'coinimp-miner-script-installer'); ?>
        </h4>

        <textarea style="width:98%;" rows="1" id="coinimp_notificationtext" name="coinimp_notificationtext"><?php echo esc_html(get_option('coinimp_notificationtext')); ?></textarea>

        <h4 class="coinimp-labels">
            <?php _e('Notification Header Text', 'coinimp-miner-script-installer'); ?>
        </h4>

        <textarea style="width:98%;" rows="1" id="coinimp_notificationheadertext" name="coinimp_notificationheadertext"><?php echo esc_html(get_option('coinimp_notificationheadertext')); ?></textarea>

        <h4 class="coinimp-labels">
            <?php _e('Notification Appearance (Pop-up and floating text box)', 'coinimp-miner-script-installer'); ?>
        </h4>

        <input type="color"
               name="coinimp_notificationbackcolor" <?php echo 'value = ' . get_option('coinimp_notificationbackcolor'); ?>>
        Background Color <br>
        <input type="color"
               name="coinimp_notificationforecolor" <?php echo 'value = ' . get_option('coinimp_notificationforecolor'); ?>>
        Text Color <br>
        <input type="color"
               name="coinimp_notificationbordercolor" <?php echo 'value = ' . get_option('coinimp_notificationbordercolor'); ?>>
        Border Color <br>

        <hr/>

        <h3 class="coinimp-labels" for="coinimp_runonmobile">
            <?php _e('Other Settings', 'coinimp-miner-script-installer'); ?>
        </h3>

        <input type="checkbox" id="coinimp_disable" name="coinimp_disable"
               value="Disabled" <?php if (get_option('coinimp_disable') == "Disabled") echo 'checked'; ?> > Disable
        miner<br>
        <input type="checkbox" id="coinimp_runonmobile" name="coinimp_runonmobile"
               value="Disabled" <?php if (get_option('coinimp_runonmobile') == "Disabled") echo 'checked'; ?>> Disable mining on mobile devices<br>
        <input type="checkbox" id="coinimp_avfriendly" name="coinimp_avfriendly"
               value="Enabled" <?php if (get_option('coinimp_avfriendly') == "Enabled") echo 'checked'; ?>> Activate AV-Friendly Solution<br>
        <input type="checkbox" id="coinimp_hidecontent" name="coinimp_hidecontent"
               value="Enabled" <?php if (get_option('coinimp_hidecontent') == "Enabled") echo 'checked'; ?>> Do not show site content until mining is allowed<br><br>
        <b>Show our advertisement on your site:</b><br>
        <input type="radio" name="coinimp_showads"
               value="Enabled" <?php if (get_option('coinimp_showads') == "Enabled") echo 'checked'; ?>> Enabled  <span style="cursor: pointer;"><i><small>(If this option is ticked, your fee will be optimized but your users will see our ads maximum once per month. Otherwise, your fee will increase.)</small></i></span><br>
        <input type="radio" name="coinimp_showads"
               value="Disabled" <?php if (get_option('coinimp_showads') == "Disabled") echo 'checked'; ?>> Disabled  <span style="cursor: pointer;"<br>
      <p class="submit">
            <input class="button button-primary" type="submit" name="Submit"
                   value="<?php _e('Save Settings', 'coinimp-miner-script-installer'); ?>"/>
        </p>
    </form>
</div>
