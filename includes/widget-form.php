<?php
/*
 *  @description: Widget form options in WP-Admin
 *  @since 1.2.0
 *  @created: 04/10/13
 */
?>

<!-- Title -->
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
</p>

<!-- Listing Options -->
<p class="widget-api-option">
    <label for="<?php echo $this->get_field_id('display_option'); ?>"><?php _e('Yelp API Request Method:', 'ywp'); ?></label><br/>
    <span class="yelp-method-span search-api-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name('display_option'); ?>" class="<?php echo $this->get_field_id('display_option'); ?> search-api-option" value="0" <?php checked('0', $displayOption); ?>><span class="yelp-method-label"><?php _e('Search Method', 'ywp'); ?></span><br/>
    </span>
    <span class="yelp-method-span business-api-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name('display_option'); ?>" class="<?php echo $this->get_field_id('display_option'); ?> business-api-option" value="1" <?php checked('1', $displayOption); ?>><span class="yelp-method-label"><?php _e('Business Method', 'ywp'); ?></span>
    </span>
</p>


<div class="toggle-api-option-1 toggle-item <?php if ($displayOption == "0") {
    echo 'toggled';
} ?>">
    <!-- Search Term -->
    <p>
        <label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Search Term:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('term'); ?>" name="<?php echo $this->get_field_name('term'); ?>" type="text" value="<?php echo $term; ?>"/>
    </p>


    <!-- Location -->
    <p>
        <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text" value="<?php echo $location; ?>"/>
    </p>

    <!-- Limit -->
    <p>
        <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of Items:'); ?></label>
        <select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" class="widefat">
            <?php
            $options = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
            foreach ($options as $option) {
                echo '<option value="' . $option . '" id="' . $option . '"', $limit == $option ? ' selected="selected"' : '', '>', $option, '</option>';
            }
            ?>
        </select>
    </p>

    <!-- Sort -->
    <p>
        <label for="<?php echo $this->get_field_id('sort'); ?>"><?php _e('Sorting:'); ?></label>
        <select name="<?php echo $this->get_field_name('sort'); ?>" id="<?php echo $this->get_field_id('sort'); ?>" class="widefat">
            <?php
            $options = array(__('Best Match', 'ywp'), __('Distance', 'ywp'), __('Highest Rated', 'ywp'));
            //Counter for Option Values
            $counter = 0;

            foreach ($options as $option) {
                echo '<option value="' . $counter . '" id="' . $option . '"', $sort == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                $counter++;
            }
            ?>
        </select>
    </p>

</div><!-- /.toggle-api-option-1 -->


<div class="toggle-api-option-2 toggle-item  <?php if ($displayOption == "1") {
    echo 'toggled';
} ?>">
    <!-- Business ID -->
    <p>
        <label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Business ID:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo $id; ?>"/>
    </p>
</div>


<h4 class="yelp-toggler"><?php _e('Display Options:', 'ywp'); ?><span></span></h4>

<div class="display-options toggle-item">


    <!-- Disable title output checkbox -->
    <p>
        <input id="<?php echo $this->get_field_id('display_address'); ?>" name="<?php echo $this->get_field_name('display_address'); ?>" type="checkbox" value="1" <?php checked('1', $address); ?>/>
        <label for="<?php echo $this->get_field_id('display_address'); ?>"><?php _e('Display Business Address', 'ywp'); ?></label>
    </p>

</div>

<h4 class="yelp-toggler">Advanced Options: <span></span></h4>

<div class="advanced-options toggle-item">

    <!-- Disable title output checkbox -->
    <p>
        <input id="<?php echo $this->get_field_id('disable_title_output'); ?>" name="<?php echo $this->get_field_name('disable_title_output'); ?>" type="checkbox" value="1" <?php checked('1', $titleOutput); ?>/>
        <label for="<?php echo $this->get_field_id('disable_title_output'); ?>"><?php _e('Disable Title Output', 'ywp'); ?></label>
    </p>

    <!-- Open Links in New Window -->
    <p>
        <input id="<?php echo $this->get_field_id('target_blank'); ?>" name="<?php echo $this->get_field_name('target_blank'); ?>" type="checkbox" value="1" <?php checked('1', $targetBlank); ?>/>
        <label for="<?php echo $this->get_field_id('target_blank'); ?>"><?php _e('Open Links in New Window', 'ywp'); ?></label>
    </p>
    <!-- No Follow Links -->
    <p>
        <input id="<?php echo $this->get_field_id('no_follow'); ?>" name="<?php echo $this->get_field_name('no_follow'); ?>" type="checkbox" value="1" <?php checked('1', $noFollow); ?>/>
        <label for="<?php echo $this->get_field_id('no_follow'); ?>"><?php _e('No Follow Links', 'ywp'); ?></label>
    </p>

    <!-- Transient / Cache -->
    <p>
        <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Cache Data:'); ?></label>
        <select name="<?php echo $this->get_field_name('cache'); ?>" id="<?php echo $this->get_field_id('cache'); ?>" class="widefat">
            <?php
            $options = array(__('None', 'ywp'), __('1 Hour', 'ywp'), __('3 Hours', 'ywp'), __('6 Hours', 'ywp'), __('12 Hours', 'ywp'), __('1 Day', 'ywp'), __('2 Days', 'ywp'), __('1 Week', 'ywp'));

            foreach ($options as $option) {
                echo '<option value="' . $option . '" id="' . $option . '"', $cache == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                $counter++;
            }
            ?>
        </select>
    </p>


</div>
