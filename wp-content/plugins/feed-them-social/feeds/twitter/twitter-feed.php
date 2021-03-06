<?php
namespace feedthemsocial;
class FTS_Twitter_Feed extends feed_them_social_functions
{
    /**
     * Construct
     * Added Since 9/28/2016 https://dev.twitter.com/overview/api/upcoming-changes-to-tweets
     *
     * Twitter Feed constructor.
     *
     * @since 1.9.6
     */
    function __construct()
    {
        add_shortcode('fts_twitter', array($this, 'fts_twitter_func'));
        add_action('wp_enqueue_scripts', array($this, 'fts_twitter_head'));
    }

    /**
     * FTS Twitter Head
     *
     * Add Styles and Scripts functions.
     *
     * @since 1.9.6
     */
    function fts_twitter_head()
    {
        wp_enqueue_style('fts-feeds', plugins_url('feed-them-social/feeds/css/styles.css'));
    }

    /**
     * FTS Twitter Function
     *
     * Display Twitter Feed.
     *
     * @param $atts
     * @return mixed
     * @since 1.9.6
     */
    function fts_twitter_func($atts)
    {

        global $connection;
        $twitter_show_follow_btn = get_option('twitter_show_follow_btn');
        $twitter_show_follow_btn_where = get_option('twitter_show_follow_btn_where');
        $twitter_show_follow_count = get_option('twitter_show_follow_count');
        $fts_twitter_hide_images_in_posts = get_option('fts_twitter_hide_images_in_posts');
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        //$fts_functions = new feed_them_social_functions;
        $twitter_allow_shortlink_conversion = get_option('twitter_allow_shortlink_conversion');
        $twitter_allow_videos = get_option('twitter_allow_videos');

        if (isset($twitter_allow_shortlink_conversion) && $twitter_allow_shortlink_conversion == 'yes' && isset($twitter_allow_shortlink_conversion) && $twitter_allow_shortlink_conversion == 'yes') {
            wp_enqueue_script('fts-longurl-js', plugins_url('feed-them-social/feeds/js/jquery.longurl.js'));
        }
        // option to allow this action or not from the Twitter Options page
        if (is_plugin_active('feed-them-premium/feed-them-premium.php')) {

            include WP_CONTENT_DIR . '/plugins/feed-them-premium/feeds/twitter/twitter-feed.php';

            if ($popup == 'yes') {
                // it's ok if these styles & scripts load at the bottom of the page
                $fts_fix_magnific = get_option('fts_fix_magnific') ? get_option('fts_fix_magnific') : '';
                if (isset($fts_fix_magnific) && $fts_fix_magnific !== '1') {
                    wp_enqueue_style('fts-popup', plugins_url('feed-them-social/feeds/css/magnific-popup.css'));
                }
                wp_enqueue_script('fts-popup-js', plugins_url('feed-them-social/feeds/js/magnific-popup.js'));
            }
        } else {
            extract(shortcode_atts(array(
                'twitter_name' => '',
                'twitter_height' => '',
                'tweets_count' => '',
                'description_image' => '',
                'search' => '',
                'show_retweets' => '',
                'cover_photo' => '',
                'stats_bar' => '',
            ), $atts));
        }
        $numTweets = $tweets_count;
        if ($numTweets == NULL) {
            $numTweets = '5';
        }

        if (!is_plugin_active('feed-them-premium/feed-them-premium.php') && $numTweets > '6') {
            $numTweets = '6';
        }

        $name = $twitter_name;
        $excludeReplies = true;
        ob_start();

        if (!empty($search)) {
            $data_cache = 'twitter_data_cache_' . $search . '_num' . $numTweets . '';
        } else {
            $data_cache = 'twitter_data_cache_' . $name . '_num' . $numTweets . '';
        }

        //Check Cache
        if (false !== ($transient_exists = $this->fts_check_feed_cache_exists($data_cache))) {
            $fetchedTweets = $this->fts_get_feed_cache($data_cache);
            $cache_used = true;
        } else {
            include_once WP_CONTENT_DIR . '/plugins/feed-them-social/feeds/twitter/twitteroauth/twitteroauth.php';
            $fts_twitter_custom_consumer_key = get_option('fts_twitter_custom_consumer_key');
            $fts_twitter_custom_consumer_secret = get_option('fts_twitter_custom_consumer_secret');
            $fts_twitter_custom_access_token = get_option('fts_twitter_custom_access_token');
            $fts_twitter_custom_access_token_secret = get_option('fts_twitter_custom_access_token_secret');
            //Use custom api info
            if (!empty($fts_twitter_custom_consumer_key) && !empty($fts_twitter_custom_consumer_secret) && !empty($fts_twitter_custom_access_token) && !empty($fts_twitter_custom_access_token_secret)) {
                $connection = new TwitterOAuthFTS(
                //Consumer Key
                    $fts_twitter_custom_consumer_key,
                    //Consumer Secret
                    $fts_twitter_custom_consumer_secret,
                    //Access Token
                    $fts_twitter_custom_access_token,
                    //Access Token Secret
                    $fts_twitter_custom_access_token_secret
                );
            } //else use default info
            else {
                $connection = new TwitterOAuthFTS(
                //Consumer Key
                    'dOIIcGrhWgooKquMWWXg',
                    //Consumer Secret
                    'qzAE4t4xXbsDyGIcJxabUz3n6fgqWlg8N02B6zM',
                    //Access Token
                    '1184502104-Cjef1xpCPwPobP5X8bvgOTbwblsmeGGsmkBzwdB',
                    //Access Token Secret
                    'd789TWA8uwwfBDjkU0iJNPDz1UenRPTeJXbmZZ4xjY'
                );
            }
            // $videosDecode = 'https://api.twitter.com/1.1/statuses/oembed.json?id=507185938620219395';
            // If excluding replies, we need to fetch more than requested as the
            // total is fetched first, and then replies removed.
            $totalToFetch = ($excludeReplies) ? max(50, $numTweets * 3) : $numTweets;
            $description_image = !empty($description_image) ? $description_image : "";
            $show_retweets = !empty($show_retweets) ? $show_retweets : "1";
            if (!empty($show_retweets) && $show_retweets == 'yes') {
                $show_retweets = '1';
            }
            if (!empty($show_retweets) && $show_retweets == 'no') {
                $show_retweets = '0';
            }
            // $url_of_status = !empty($url_of_status) ? $url_of_status : "";
            // $widget_type_for_videos = !empty($widget_type_for_videos) ? $widget_type_for_videos : "";
            if (!empty($search)) {
                $fetchedTweets = $connection->get(
                    'search/tweets',
                    array(
                        'q' => $search,
                        'count' => $totalToFetch,//
                        'result_type' => 'recent',
                        'include_rts' => $show_retweets,
                        'tweet_mode' => 'extended',
                    )
                );
            } else {
                $fetchedTweets = $connection->get(
                    'statuses/user_timeline',
                    array(
                        'tweet_mode' => 'extended',
                        'screen_name' => $name,
                        'count' => $totalToFetch,
                        'exclude_replies' => $excludeReplies,
                        'images' => $description_image,
                        'include_rts' => $show_retweets,
                    )
                );
            }
             //   echo'<pre>';
             //   print_r($fetchedTweets);
             //   echo'</pre>';

            if (!empty($search)) {
                $fetchedTweets = $fetchedTweets->statuses;
            } else {
                $fetchedTweets = $fetchedTweets;
            }

            // $screen_name = $q;

        }//END ELSE
        //Error Check
        if (isset($fetchedTweets->errors)) {
            $error_check = __('Oops, Somethings wrong. ', 'feed-them-social') . $fetchedTweets->errors[0]->message;
            if ($fetchedTweets->errors[0]->code == 32) {
                $error_check .= __(' Please check that you have entered your Twitter API token information correctly on the Twitter Options page of Feed Them Social.', 'feed-them-social');
            }
            if ($fetchedTweets->errors[0]->code == 34) {
                $error_check .= __(' Please check the Twitter Username you have entered is correct in your shortcode for Feed Them Social.', 'feed-them-social');
            }
        } elseif (empty($fetchedTweets) && !isset($fetchedTweets->errors)) {
            $error_check = __(' This account has no tweets. Please Tweet to see this feed. Feed Them Social.', 'feed-them-social');
        }
        //IS RATE LIMIT REACHED?
        if (isset($fetchedTweets->errors) && $fetchedTweets->errors[0]->code !== 32 && $fetchedTweets->errors[0]->code !== 34) {
            _e('Rate Limited Exceeded. Please go to the Feed Them Social Plugin then the Twitter Options page and follow the instructions under the header Twitter API Token.', 'feed-them-social');
        }
        // Did the fetch fail?
        if (isset($error_check)) {
            echo $error_check;
        }//END IF
        else {
            if (!empty($fetchedTweets)) {
                //Cache It
                if (!isset($cache_used)) {
                    $this->fts_create_feed_cache($data_cache, $fetchedTweets);
                }
                // Fetch succeeded.
                // Now update the array to store just what we need.
                // (Done here instead of PHP doing this for every page load)
                $limitToDisplay = min($numTweets, count($fetchedTweets));
                for ($i = 0; $i < $limitToDisplay; $i++) {
                    $tweet = $fetchedTweets[$i];
                    // Core info.
                    $name = isset($tweet->user->name) ? $tweet->user->name : "";
                    $screen_name = isset($tweet->user->screen_name) ? $tweet->user->screen_name : "";
                    $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
                    $not_protocol = !isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
                    $permalink = $protocol . 'twitter.com/' . $screen_name . '/status/' . $tweet->id_str;
                    $user_permalink = $protocol . 'twitter.com/#!/' . $screen_name;
                    //Is Media Set

                    if (!empty($tweet->entities->media[0]->media_url)) {
                        $media_url = $tweet->entities->media[0]->media_url_https;
                        // $media_url = str_replace($not_protocol, $protocol, $media_url);

                    } elseif(!empty($tweet->retweeted_status->extended_entities->media[0]->media_url_https)) {
                        $media_url = $tweet->retweeted_status->extended_entities->media[0]->media_url_https;
                    }
                    else {
                        $media_url = '';
                    }

                    //  $widget_type_for_videos = $tweet->widget_type_for_videos;
                    /* Alternative image sizes method: http://dev.twitter.com/doc/get/users/profile_image/:screen_name */
                    $image = isset($tweet->user->profile_image_url) ? $tweet->user->profile_image_url : "";
                    $image = str_replace($not_protocol, $protocol, $image);
                    // Message. Convert links to real links.
                    $pattern = array('/http:(\S)+/', '/https:(\S)+/', '/([^a-zA-Z0-9-_&])@([0-9a-zA-Z_]+)/', '/([^a-zA-Z0-9-_&])#([0-9a-zA-Z_]+)/');
                    $replace = array(' <a href="${0}" target="_blank" rel="nofollow">${0}</a>', ' <a href="${0}" target="_blank" rel="nofollow">${0}</a>', ' <a href="' . $protocol . 'twitter.com/$2" target="_blank" rel="nofollow">@$2</a>', ' <a href="' . $protocol . 'twitter.com/search?q=%23$2&src=hash" target="_blank" rel="nofollow">#$2</a>');
                    $full_text = preg_replace($pattern, $replace, $tweet->full_text);
                    // Need to get time in Unix format.
                    $times = isset($tweet->created_at) ? $tweet->created_at : "";

                    $ftsCustomDate = get_option('fts-custom-date');
                    $ftsCustomTime = get_option('fts-custom-time');
                    $CustomDateCheck = get_option('fts-date-and-time-format');

                    if ($ftsCustomDate == '' && $ftsCustomTime == '') {
                        $CustomDateFormatTwitter = get_option('fts-date-and-time-format');
                    } else if ($ftsCustomDate !== '' || $ftsCustomTime !== '') {
                        $CustomDateFormatTwitter = get_option('fts-custom-date') . ' ' . get_option('fts-custom-time');
                    } else {
                        $CustomDateFormatTwitter = 'F jS, Y \a\t g:ia';
                    }
                    date_default_timezone_set(get_option('fts-timezone'));

                    $fts_twitter_offset_time = get_option('fts_twitter_time_offset');

                    if ($fts_twitter_offset_time == 1) {
                        $fts_twitter_offset_time_final = strtotime($times);
                    } else {
                        $fts_twitter_offset_time_final = strtotime($times) - 3 * 3600;
                    }

                    if ($CustomDateCheck == 'one-day-ago') {
                        $times = $fts_twitter_offset_time_final;
                        $date = date_create(date('F jS, Y g:ia', $times));
                        $timestamp = date_timestamp_get($date);
                        $getFtsAgo = new feed_them_social_functions();
                        $uTime = $getFtsAgo->fts_ago($times);
                    } else {
                        $uTime = date_i18n($CustomDateFormatTwitter, $fts_twitter_offset_time_final);
                    }

                    $id = isset($tweet->id) ? $tweet->id : "";
                    $fts_twitter_full_width = get_option('twitter_full_width');

                    $statuses_count = isset($tweet->user->statuses_count) ? $tweet->user->statuses_count : "";
                    $followers_count = isset($tweet->user->followers_count) ? $tweet->user->followers_count : "";
                    $friends_count = isset($tweet->user->friends_count) ? $tweet->user->friends_count : "";
                    $favourites_count = isset($tweet->user->favourites_count) ? $tweet->user->favourites_count : "";

                    // the retweet count works for posts and retweets
                    $retweet_count = isset($tweet->retweet_count) ? $tweet->retweet_count : "";

                    // the favorites count needs to be switched up for retweets
                    if(empty($tweet->retweeted_status->favorite_count)){
                       $favorite_count = $tweet->favorite_count;
                    }
                    else {
                       $favorite_count = $tweet->retweeted_status->favorite_count;
                    }

                    $profile_banner_url = isset($tweet->user->profile_banner_url) ? $tweet->user->profile_banner_url : "";
                    // not using right now
                    // $listed_count = isset($tweet->user->listed_count) ? $tweet->user->listed_count : "";
                    // $profile_image_url_https = isset($tweet->user->profile_image_url_https) ? $tweet->user->profile_image_url_https : "";
                    // $profile_background_image_url_https = isset($tweet->user->profile_background_image_url_https) ? $tweet->user->profile_background_image_url_https : "";

                    // $urls string is used so we can parse out video files
                    $urls = isset($tweet->entities->urls[0]->expanded_url) ? $tweet->entities->urls[0]->expanded_url : "";
                    // Now make the new array.
                    $tweets[] = array(
                        'full_text' => $full_text,
                        'name' => $name,
                        'screen_name' => $screen_name,
                        'user_permalink' => $user_permalink,
                        'permalink' => $permalink,
                        'image' => $image,
                        'time' => $uTime,
                        'media_url' => $media_url,
                        'id' => $id,
                        'urls' => $urls,
                        'retweet_count' => $retweet_count,
                        'favorite_count' => $favorite_count,
                    );
                }//End FOR fts-twitter-full-width
              //   echo '<pre>';
              //    print_r($tweets);
              //   echo '</pre>';
                $twitter_allow_shortlink_conversion = get_option('twitter_allow_shortlink_conversion');

                ?>
                <div id="twitter-feed-<?php print $twitter_name ?>" class="fts-twitter-div<?php if ($twitter_height !== 'auto' && empty($twitter_height) == NULL) { ?> fts-twitter-scrollable<?php }
                if (isset($popup) && $popup == 'yes') { ?> popup-gallery-twitter<?php } ?>" <?php if ($twitter_height !== 'auto' && empty($twitter_height) == NULL) { ?>style="height:<?php echo $twitter_height; ?>"<?php } ?>>

                    <?php
                    //******************
                    // SOCIAL BUTTON IF COVER PHOTO ON
                    //******************
                    if (!empty($search)) {
                        $screen_name = $twitter_name;
                    }
                    if (isset($profile_banner_url) && isset($cover_photo) && $cover_photo == "yes") {
                        ?>
                        <div class="fts-twitter-backg-image">
                            <?php
                            if (isset($twitter_show_follow_btn) && $twitter_show_follow_btn == 'yes' && $twitter_show_follow_btn_where == 'twitter-follow-above' && $twitter_name !== '') {
                                echo '<div class="twitter-social-btn-top">';
                                $this->social_follow_button('twitter', $screen_name);
                                echo '</div>';
                            }
                            ?>
                            <img src="<?php print $profile_banner_url; ?>"/>

                        </div>
                    <?php } elseif (isset($twitter_show_follow_btn) && $twitter_show_follow_btn == 'yes' && $twitter_show_follow_btn_where == 'twitter-follow-above' && $twitter_name !== '' && $cover_photo !== "yes") {
                        echo '<div class="twitter-social-btn-top">';
                        $this->social_follow_button('twitter', $screen_name);
                        echo '</div>';
                    }// if cover photo = yes


                    // These need to be in this order to keep the different counts straight since I used either $statuses_count or $followers_count throughout.

                    // here we add a , for all numbers below 9,999
                    if (isset($statuses_count) && $statuses_count <= 9999) {
                        $statuses_count = number_format($statuses_count);
                    }
                    // here we convert the number for the like count like 1,200,000 to 1.2m if the number goes into the millions
                    if (isset($statuses_count) && $statuses_count >= 1000000) {
                        $statuses_count = round(($statuses_count / 1000000), 1) . 'm';
                    }
                    // here we convert the number for the like count like 10,500 to 10.5k if the number goes in the 10 thousands
                    if (isset($statuses_count) && $statuses_count >= 10000) {
                        $statuses_count = round(($statuses_count / 1000), 1) . 'k';
                    }

                    // here we add a , for all numbers below 9,999
                    if (isset($followers_count) && $followers_count <= 9999) {
                        $followers_count = number_format($followers_count);
                    }
                    // here we convert the number for the comment count like 1,200,000 to 1.2m if the number goes into the millions
                    if (isset($followers_count) && $followers_count >= 1000000) {
                        $followers_count = round(($followers_count / 1000000), 1) . 'm';
                    }
                    // here we convert the number  for the comment count like 10,500 to 10.5k if the number goes in the 10 thousands
                    if (isset($followers_count) && $followers_count >= 10000) {
                        $followers_count = round(($followers_count / 1000), 1) . 'k';
                    }


                    // option to allow the followers plus count to show
                    if (isset($twitter_show_follow_count) && $twitter_show_follow_count == 'yes' && $search == '' && isset($stats_bar) && $stats_bar !== "yes") {
                        print '<div class="twitter-followers-fts-singular"><a href="' . $user_permalink . '" target="_blank">' . __('Followers:', 'feed-them-social') . '</a> ' . $followers_count . '</div>';
                    }
                    if (isset($stats_bar) && $stats_bar == "yes" && $search == '') {
                        // option to allow the followers plus count to show

                        print '<div class="fts-twitter-followers-wrap">';
                        print '<div class="twitter-followers-fts fts-tweets-first"><a href="' . $user_permalink . '" target="_blank">' . __('Tweets', 'feed-them-social') . '</a> ' . $statuses_count . '</div>';
                        print '<div class="twitter-followers-fts fts-following-link-div"><a href="' . $user_permalink . '" target="_blank">' . __('Following', 'feed-them-social') . '</a> ' . number_format($friends_count) . '</div>';
                        print '<div class="twitter-followers-fts fts-followers-link-div"><a href="' . $user_permalink . '" target="_blank">' . __('Followers', 'feed-them-social') . '</a> ' . $followers_count . '</div>';
                        print '<div class="twitter-followers-fts fts-likes-link-div"><a href="' . $user_permalink . '" target="_blank">' . __('Likes', 'feed-them-social') . '</a> ' . number_format($favourites_count) . '</div>';
                        print '</div>';

                    } ?>
                    <?php foreach ($tweets as $t) :
                        $twitter_allow_videos = get_option('twitter_allow_videos');
                    if (!empty($t['urls']) && $twitter_allow_videos !== 'no') {
                        $type = 'twitterFeed';
                        $fts_dynamic_vid_name_string = trim($this->rand_string_twitter(10) . '_' . $type);
                        $fts_dynamic_name = '';
                        if (isset($fts_dynamic_vid_name_string)) {
                            $fts_dynamic_name = 'feed_dynamic_class' . $fts_dynamic_vid_name_string;
                        }
                        // ajax part
                        $time = time();
                        $nonce = wp_create_nonce($time . "load-more-nonce");
                        ?>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                <?php
                                // option to allow this action or not from the Twitter Options page
                                if (isset($twitter_allow_shortlink_conversion) && $twitter_allow_shortlink_conversion == 'yes') {
                                    // API that converts shortlinks. longurlplease.com
                                    print 'jQuery.longurlplease();';
                                } ?>
                                jQuery('.<?php echo $fts_dynamic_name ?> a.fts-twitter-load-video').click(function () {
                                    var fts_link = jQuery(this).attr('href');
                                    var fts_security = "<?php echo $nonce;?>";
                                    var fts_post_id = "<?php echo $t['id'];?>";
                                    var fts_time = "<?php echo $time;?>";
                                    console.log('Submit Function');
                                    jQuery.ajax({
                                        data: {
                                            action: "fts_load_videos",
                                            fts_link: fts_link,
                                            fts_security: fts_security,
                                            fts_time: fts_time,
                                            fts_post_id: fts_post_id
                                        },
                                        type: 'GET',
                                        url: myAjaxFTS,
                                        beforeSend: function () {
                                            jQuery('.<?php echo $fts_dynamic_name ?> .fts-video-loading-notice').fadeIn();
                                        },
                                        success: function (data) {
                                            jQuery('.<?php echo $fts_dynamic_name ?> .fts-video-loading-notice').hide();
                                            jQuery('.<?php echo $fts_dynamic_name ?> .fts-video-wrapper').append(data).filter('.<?php echo $fts_dynamic_name ?> .fts-video-wrapper').html();
                                            jQuery('a.<?php echo $fts_dynamic_name ?> .twitter-video').hide();
                                            jQuery('a.<?php echo $fts_dynamic_name ?>').hide();
                                            jQuery('a.<?php echo $fts_dynamic_name ?>.fts-close-media').show();
                                            jQuery('.<?php echo $fts_dynamic_name ?> .fts-video-wrapper-padding').slideDown();
                                            console.log('Well Done and got this from sever: ' + data);
                                        }
                                    }); // end of ajax()
                                    return false;
                                });
                                jQuery('.<?php echo $fts_dynamic_name ?> .fts-close-media').click(function () {
                                    jQuery('a.<?php echo $fts_dynamic_name ?>.fts-close-media span.fts-show-media-text').toggle();
                                    jQuery('a.<?php echo $fts_dynamic_name ?>.fts-close-media span.fts-hide-media-text').toggle();
                                    jQuery('.<?php echo $fts_dynamic_name ?> .fts-video-wrapper-padding').slideToggle();
                                    return false;
                                })
                            });
                        </script>
                    <?php } // END if not empty $t['urls']

                    $fts_dynamic_name = isset($fts_dynamic_name) ? $fts_dynamic_name : ''; ?>
                        <div class="fts-tweeter-wrap <?php echo $fts_dynamic_name; ?>">
                            <div class="tweeter-info">
                                <?php if ($fts_twitter_full_width !== 'yes') { ?>
                                    <div class="fts-twitter-image">
                                        <a href="<?php print $t['user_permalink']; ?>" target="_blank" class="black"><img class="twitter-image" src="<?php print $t['image']; ?>" alt="<?php print $t['screen_name']; ?>"/></a>
                                    </div>
                                <?php } ?>
                                <div class="<?php if ($fts_twitter_full_width == 'yes') { ?>fts-twitter-full-width<?php } else { ?>right<?php } ?>">
                                    <div class="uppercase bold">
                                        <a href="<?php print $t['user_permalink']; ?>" target="_blank" class="fts-twitter-full-name"><?php print $t['name']; ?></a>
                                        <a href="<?php print $t['user_permalink']; ?>" target="_blank" class="fts-twitter-at-name">@<?php print $t['screen_name']; ?></a>
                                    </div>
                                    <span class="time"><a href="<?php print $t['permalink'] ?>" target="_blank"><?php print $t['time']; ?></a></span><br/>
                                        <span class="fts-twitter-text"><?php print nl2br($t['full_text']); ?>
                                            <div class="fts-twitter-caption">
                                                <a href="<?php print $t['permalink'] ?>" class="fts-view-on-twitter-link" target="_blank"><?php echo _e('View on Twitter', 'feed-them-social'); ?></a>
                                            </div>
                                        </span>
                                    <?php if ($t['media_url'] !== '' && isset($fts_twitter_hide_images_in_posts) && $fts_twitter_hide_images_in_posts !== 'yes') { ?>
                                        <a href="<?php if (isset($popup) && $popup == 'yes') {
                                            print $t['media_url'];
                                        } else {
                                            print $t['permalink'];
                                        } ?>" class="fts-twitter-link-image" target="_blank"><img class="fts-twitter-description-image" src="<?php print $t['media_url']; ?>" alt="<?php print $t['screen_name']; ?> photo"/></a> <?php }
                                    $tFinal = $t['urls'];

                                    $twitter_allow_videos = get_option('twitter_allow_videos');
                                    if ($twitter_allow_videos !== 'no') {

                                        if (!empty($tFinal)) {
                                            // && strpos($tFinal, 'vimeo') > 0 || strpos($tFinal, 'amp.twimg.com') > 0 || strpos($tFinal, 'youtube') > 0 || strpos($tFinal, 'youtu.be') > 0
                                            print '<div class="fts-video-wrapper-padding"><div class="fts-video-wrapper"></div></div>';
                                            print '<div class="fts-video-show-hide-btns-wrap ' . $fts_dynamic_name . '_bts_wrap" style="display:none">';
                                            print '<a href="' . $tFinal . '" class="fts-twitter-load-video-wrapper fts-twitter-load-video ' . $fts_dynamic_name . '" onclick="return false;">' . __('Show Media', 'feed-them-social') . '<span class="fts-video-loading-notice" style="display:none; ">' . __(', Loading...', 'feed-them-social') . '</span></a>';
                                            print '<a href="' . $tFinal . '" class="fts-twitter-load-video-wrapper fts-close-media ' . $fts_dynamic_name . '" onclick="return false;"><span class="fts-hide-media-text">' . __('Hide Media', 'feed-them-social') . '</span><span class="fts-show-media-text">' . __('Show Media', 'feed-them-social') . '</span></a>';
                                            print '</div>';
                                            ?>
                                            <script type="text/javascript">
                                                setTimeout(function () {
                                                    var url = jQuery("a.fts-twitter-load-video-wrapper.<?php echo $fts_dynamic_name ?>").attr("href");
                                                    if (url.indexOf("soundcloud") >= 0 || url.indexOf("vine") >= 0 || url.indexOf("vimeo") >= 0 || url.indexOf("youtube") >= 0 || url.indexOf("youtu.be") >= 0 || url.indexOf("amp.twimg.com") >= 0 || url.indexOf("vine") >= 0) {
                                                        if (url.indexOf("vimeo.com/blog/") >= 0 || url.indexOf("vimeo.com/ondemand/") >= 0 || url.indexOf("-youtube") >= 0 || url.indexOf("-vine") >= 0 || url.indexOf("-soundcloud") >= 0) {
                                                            jQuery('.fts-video-show-hide-btns-wrap.<?php echo $fts_dynamic_name ?>_bts_wrap').hide();
                                                        }
                                                        else {
                                                            jQuery('.fts-video-show-hide-btns-wrap.<?php echo $fts_dynamic_name ?>_bts_wrap').show();
                                                        }
                                                    }
                                                }, 1000);
                                            </script>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="fts-twitter-reply-wrap <?php if ($fts_twitter_full_width == 'yes') { ?>fts-twitter-full-width<?php } else { ?>fts-twitter-no-margin-left<?php } ?>">
                                    <div class="fts-tweet-reply-left">
                                        <a href="<?php print $t['permalink'] ?>" target="_blank">
                                            <div class="fts-twitter-reply"></div>
                                        </a>
                                    </div>
                                    <div class="fts-tweet-others-right">
                                        <a href="https://twitter.com/intent/retweet?tweet_id=<?php print $t['id'] ?>&related=<?php print $t['name'] ?>" target="_blank" class="fts-twitter-retweet-wrap">
                                            <div class="fts-twitter-retweet"><?php if ($t['retweet_count'] !== 0) {
                                                    print $t['retweet_count'];
                                                } ?></div>
                                        </a>
                                        <a href="https://twitter.com/intent/like?tweet_id=<?php print $t['id'] ?>&related=<?php print $t['name'] ?>" target="_blank" class="fts-twitter-favorites-wrap">
                                            <div class="fts-twitter-favorites"><?php  if ($t['favorite_count'] !== 0) {
                                                    print $t['favorite_count'];
                                                } ?></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="clear"></div>
                </div>
                <?php if ($twitter_height !== 'auto' && empty($twitter_height) == NULL) { ?>
                    <script>
                        // this makes it so the page does not scroll if you reach the end of scroll bar or go back to top
                        jQuery.fn.isolatedScrollTwitter = function () {
                            this.bind('mousewheel DOMMouseScroll', function (e) {
                                var delta = e.wheelDelta || (e.originalEvent && e.originalEvent.wheelDelta) || -e.detail,
                                    bottomOverflow = this.scrollTop + jQuery(this).outerHeight() - this.scrollHeight >= 0,
                                    topOverflow = this.scrollTop <= 0;
                                if ((delta < 0 && bottomOverflow) || (delta > 0 && topOverflow)) {
                                    e.preventDefault();
                                }
                            });
                            return this;
                        };
                        jQuery('.fts-twitter-scrollable').isolatedScrollTwitter();
                    </script>
                <?php } ?>
                <?php
            }// END IF $fetchedTweets
        }//END ELSE
        //******************
        // SOCIAL BUTTON
        //******************
        if (isset($twitter_show_follow_btn) && $twitter_show_follow_btn == 'yes' && $twitter_show_follow_btn_where == 'twitter-follow-below' && $twitter_name !== '') {
            echo '<div class="twitter-social-btn-bottom">';
            $this->social_follow_button('twitter', $screen_name);
            echo '</div>';
        }
        return ob_get_clean();
    }

    /**
     * Random String generator
     *
     * @param int $length
     * @return string
     * @since 1.9.6
     */
    function rand_string_twitter($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}// FTS_Twitter_Feed END CLASS
?>