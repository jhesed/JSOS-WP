<?php

function likebtn_admin_votes() {

    global $likebtn_page_sizes;
    global $wpdb;

    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style("wp-jquery-ui-dialog");

    $query_parameters = array();

    $likebtn_entities = _likebtn_get_entities(true);
    // Custom item
    $likebtn_entities[LIKEBTN_ENTITY_CUSTOM_ITEM] = __('Custom item');

    // Multisite - available for super admin only
    $blogs = array();
    $blog_list = array();
    $votes_blog_id = '';
    $prefix_prepared = $wpdb->prefix;
    if (is_multisite() && is_super_admin()) {

        global $blog_id;

        $blog_list = $wpdb->get_results("
            SELECT blog_id, domain
            FROM {$wpdb->blogs}
            WHERE site_id = '{$wpdb->siteid}'
            AND spam = '0'
            AND deleted = '0'
            AND archived = '0'
            ORDER BY blog_id
        ");

        // Place current blog on the first place
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
                break;
            }
        }
        foreach ($blog_list as $blog) {
            if ($blog->blog_id != $blog_id) {
                $blogs["{$blog->blog_id}"] = get_blog_option($blog->blog_id, 'blogname') . ' - ' . $blog->domain;
            }
        }

        // Add all
        $blogs['all'] = __('All Sites');

        // Determine selected blog id
        if (isset($_GET['likebtn_blog_id'])) {
            if ($_GET['likebtn_blog_id'] == 'all') {
                $votes_blog_id = $_GET['likebtn_blog_id'];
            } else {
                // Check if blog with ID exists
                foreach ($blog_list as $blog) {
                    if ($blog->blog_id == (int)$_GET['likebtn_blog_id']) {
                        $votes_blog_id = (int)$_GET['likebtn_blog_id'];
                        break;
                    }
                }
            }
        }

        // Prepare prefix if this is not main blog
        if ($blog_id != 1) {
            $prefix_prepared = substr($wpdb->prefix, 0, strlen($wpdb->prefix)-strlen($blog_id)-1);
        }
    }

    $entity_name = '';
    if (!empty($_GET['likebtn_entity_name'])) {
        $entity_name = $_GET['likebtn_entity_name'];
    }
    $page_size = LIKEBTN_STATISTIC_PAGE_SIZE;
    if (isset($_GET['likebtn_page_size'])) {
        $page_size = LIKEBTN_STATISTIC_PAGE_SIZE;
    }
    $post_id = '';
    if (isset($_GET['likebtn_post_id'])) {
        $post_id = trim(stripcslashes($_GET['likebtn_post_id']));
    }
    $user_id = '';
    if (isset($_GET['likebtn_user_id'])) {
        $user_id = trim(stripcslashes($_GET['likebtn_user_id']));
    }
    $ip = '';
    if (isset($_GET['likebtn_ip'])) {
        $ip = trim($_GET['likebtn_ip']);
    }
    $vote_type = '';
    if (isset($_GET['likebtn_vote_type'])) {
        $vote_type = trim($_GET['likebtn_vote_type']);
    }

    // pagination
    require_once(dirname(__FILE__) . '/likebtn_like_button_pagination.class.php');

    $pagination_target = "admin.php?page=likebtn_votes";
    foreach ($_GET as $get_parameter => $get_value) {
        $pagination_target .= '&' . $get_parameter . '=' . stripcslashes($get_value);
    }

    $p = new LikeBtnLikeButtonPagination();
    $p->limit($page_size); // Limit entries per page
    $p->target($pagination_target);
    //$p->currentPage(); // Gets and validates the current page
    $p->prevLabel(__('Previous', LIKEBTN_I18N_DOMAIN));
    $p->nextLabel(__('Next', LIKEBTN_I18N_DOMAIN));

    if (!isset($_GET['paging'])) {
        $p->page = 1;
    } else {
        $p->page = $_GET['paging'];
    }

    $query_select = '';
    $query_join = '';
    // query for limit paging
    $query_limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

    // query parameters
    $query_where = '';

    // Post type and ID
    if ($entity_name && $post_id && $entity_name != LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $query_where .= ' AND v.identifier = %s ';
        $query_parameters[] = $entity_name.'_'.$post_id;
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM && $post_id) {
        $query_where .= ' AND i.ID is not NULL AND v.identifier = %s ';
        $query_parameters[] = $post_id;
    } elseif ($entity_name == LIKEBTN_ENTITY_CUSTOM_ITEM) {
        $query_where .= ' AND i.ID is not NULL ';
    } elseif ($entity_name) {
        $query_where .= ' AND v.identifier LIKE "'.esc_sql($entity_name).'_%%" ';
    } elseif ($post_id) {
        $query_where .= ' AND v.identifier LIKE "%%_%d" ';
        $query_parameters[] = $post_id;
    }

    // Order by
    $query_orderby = ' ORDER BY created_at DESC ';

    // User ID
    if ($user_id) {
        $query_where .= ' AND v.user_id = %d ';
        $query_parameters[] = $user_id;
    }

    // IP
    if ($ip) {
        $query_where .= ' AND v.ip = %s ';
        $query_parameters[] = $ip;
    }

    // Vote type
    if ($vote_type) {
        $query_where .= ' AND v.type = %d ';
        $query_parameters[] = $vote_type;
    }

    // For Multisite
    $query = '';
    if ($votes_blog_id && $votes_blog_id != 1 && $votes_blog_id != 'all') {
        $prefix = "{$prefix_prepared}{$votes_blog_id}_";
        $query = _likebtn_get_votes_sql($prefix, $query_where, $query_orderby, $query_limit, 'SQL_CALC_FOUND_ROWS '.$query_select, $query_join);
        $query_prepared = $wpdb->prepare($query, $query_parameters);
    } else if ($votes_blog_id == 'all') {
        foreach ($blog_list as $blog) {
            if ($blog->blog_id == 1) {
                $prefix = $prefix_prepared;
            } else {
                $prefix = "{$prefix_prepared}{$blog->blog_id}_";
            }
            $query_list[] = $wpdb->prepare(_likebtn_get_votes_sql($prefix, $query_where, '', '', $blog->blog_id . ' as blog_id, '.$query_select, $query_join), $query_parameters);
        }
        $query_prepared = ' SELECT SQL_CALC_FOUND_ROWS * from (' . implode(' UNION ', $query_list) . ") query {$query_orderby} {$query_limit} ";
    } else {
        $query = _likebtn_get_votes_sql($prefix_prepared, $query_where, $query_orderby, $query_limit, 'SQL_CALC_FOUND_ROWS '.$query_select, $query_join);
        if (count($query_parameters)) {
            $query_prepared = $wpdb->prepare($query, $query_parameters);
        } else {
            $query_prepared = $query;
        }
    }
    // echo "<pre>";
    // echo $query;
    // echo $query_prepared;
    // echo $wpdb->prepare($query, $query_parameters);
    // $wpdb->show_errors();
    // exit();
    $votes = $wpdb->get_results($query_prepared);

    $total_found = 0;
    if (isset($votes[0])) {
        $query_found_rows = "SELECT FOUND_ROWS() as found_rows";
        $found_rows = $wpdb->get_results($query_found_rows);

        $total_found = (int) $found_rows[0]->found_rows;

        $p->items($total_found);
        $p->calculate(); // Calculates what to show
        $p->parameterName('paging');
        $p->adjacents(1); // No. of page away from the current page
    } else {
        $votes = array();
    }

    $loader = _likebtn_get_public_url() . 'img/ajax_loader_hor.gif';

    likebtn_admin_header();
    ?>

    <script type="text/javascript">
        var likebtn_msg_ip_info = '<?php _e("IP Info", LIKEBTN_I18N_DOMAIN); ?>';
    </script>

    <div>
        <a href="javascript:jQuery('#likebtn_no_vts').toggle();void(0);"><?php _e('Do not see votes?', LIKEBTN_I18N_DOMAIN); ?></a>
        <div id="likebtn_no_vts">
            <p class="description">
                ● <?php _e('If Like button is added using HTML-code votes will not be populated into your database. The recommended way of enabling the Like buttons is via <strong>Buttons</strong> tab or <a href="https://likebtn.com/en/wordpress-like-button-plugin#shortcode" target="_blank">[likebtn] shortcode</a>.', LIKEBTN_I18N_DOMAIN); ?><br/>
                ● <?php echo strtr(
           __('Make sure not to disable anonymous access to %admin_ajax%, otherwise votes from anonymous visitors will not be accepted.', LIKEBTN_I18N_DOMAIN), 
            array('%admin_ajax%'=>'<a href="'.admin_url('admin-ajax.php').'" target="_blank">/wp-admin/admin-ajax.php</a>')) ?><br/>
                ● <?php _e('Votes for Custom items appear in the list after synchronization.', LIKEBTN_I18N_DOMAIN); ?>
            </p>
        </div>
        <br/><br/>
        <form action="" method="get" id="votes_form" autocomplete="off">
            <input type="hidden" name="page" value="likebtn_votes" />
            <div class="postbox statistics_filter_container">
                <div class="inside">
                    <label><?php _e('Item Type', LIKEBTN_I18N_DOMAIN); ?>:</label>
                    <select name="likebtn_entity_name" >
                        <option value="">-- <?php _e('Any', LIKEBTN_I18N_DOMAIN); ?> --</option>
                        <?php foreach ($likebtn_entities as $entity_name_value => $entity_title): ?>
                            <option value="<?php echo $entity_name_value; ?>" <?php selected($entity_name, $entity_name_value); ?> ><?php _e($entity_title, LIKEBTN_I18N_DOMAIN); ?></option>
                        <?php endforeach ?>
                    </select>
                    &nbsp;&nbsp;
                    <label><?php _e('Item ID', LIKEBTN_I18N_DOMAIN); ?>:</label>
                    <input type="text" name="likebtn_post_id" value="<?php echo htmlspecialchars($post_id) ?>" size="10" />
                    <br/><br/>
                    <label><?php _e('User ID', LIKEBTN_I18N_DOMAIN); ?>:</label>
                    <input type="text" name="likebtn_user_id" value="<?php echo htmlspecialchars($user_id) ?>" size="10" />
                    &nbsp;&nbsp;
                    <label><?php _e('IP'); ?>:</label>
                    <input type="text" name="likebtn_ip" value="<?php echo htmlspecialchars($ip) ?>" size="20"/>
                    &nbsp;&nbsp;
                    <label><?php _e('Vote Type', LIKEBTN_I18N_DOMAIN); ?>:</label>
                    <select name="likebtn_vote_type" >
                        <option value="">-- <?php _e('Likes & Dislikes', LIKEBTN_I18N_DOMAIN); ?> --</option>
                        <option value="1" <?php selected((int)$vote_type, 1); ?> ><?php _e('Likes', LIKEBTN_I18N_DOMAIN); ?></option>
                        <option value="-1" <?php selected((int)$vote_type, -1); ?> ><?php _e('Dislikes', LIKEBTN_I18N_DOMAIN); ?></option>
                    </select>

                    &nbsp;&nbsp;
                    <input class="button-secondary" type="button" name="reset" value="<?php _e('Reset filter', LIKEBTN_I18N_DOMAIN); ?>" onClick="jQuery('.statistics_filter_container :input[type!=button]').val('');
                jQuery('#votes_form').submit();"/>
                </div>
            </div>

            <?php if ($blogs): ?>
                <label><?php _e('Site', LIKEBTN_I18N_DOMAIN); ?>:</label>
                <select name="likebtn_blog_id" >
                    <?php foreach ($blogs as $blog_id_value => $blog_title): ?>
                        <option value="<?php echo $blog_id_value; ?>" <?php selected($votes_blog_id, $blog_id_value); ?> ><?php echo $blog_title; ?></option>
                    <?php endforeach ?>
                </select>&nbsp;&nbsp;
            <?php endif ?>
            
            <label><?php _e('Page Size', LIKEBTN_I18N_DOMAIN); ?>:</label>
            <select name="likebtn_page_size" >
                <?php foreach ($likebtn_page_sizes as $page_size_value): ?>
                    <option value="<?php echo $page_size_value; ?>" <?php selected($page_size, $page_size_value); ?> ><?php echo $page_size_value ?></option>
                <?php endforeach ?>

            </select><br/><br/>
            <input class="button-primary" type="submit" name="show" value="<?php _e('View', LIKEBTN_I18N_DOMAIN); ?>" /> 
            &nbsp;
            <?php _e('Votes Found', LIKEBTN_I18N_DOMAIN); ?>: <strong><?php echo $total_found ?></strong>
        </form>
        <br/>
        <form method="post" action="" id="votes_actions_form">
            <input type="hidden" name="bulk_action" value="" id="stats_bulk_action" />
        <?php if (count($votes) && $p->lastpage > 1): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php echo $p->show(); ?>
                </div>
            </div>
        <?php endif ?>
        <table class="widefat" id="votes_container">
            <thead>
                <tr>
                    <?php /*<th><input type="checkbox" onclick="statisticsItemsCheckbox(this)" value="all" style="margin:0"></th>*/ ?>
                    <?php if ($blogs && $votes_blog_id == 'all'): ?>
                        <th><?php _e('Site') ?></th>
                    <?php endif ?>
                    <th colspan="2"><?php _e('User', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th>IP</th>
                    <th><?php _e('Date', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th><?php _e('Type', LIKEBTN_I18N_DOMAIN) ?></th>
                    <th><?php _e('Item', LIKEBTN_I18N_DOMAIN) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($votes as $votes_item): ?>
                    <?php
                        $entity_info = _likebtn_parse_identifier($votes_item->identifier);

                        $user_name = '';
                        if ($votes_item->user_id) {
                            $user_name = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $votes_item->user_id);
                        }
                        $avatar_url = '';
                        if ($user_name) {
                            $avatar_url = _likebtn_get_avatar_url($votes_item->user_id);
                        }

                        $user_url = '';
                        if ($user_name) {
                            $user_url = _likebtn_get_entity_url(LIKEBTN_ENTITY_USER, $votes_item->user_id);
                        }

                        $item_title = '';
                        $item_url = '';
                        if ($votes_item->item_id) {
                            $item_title = $votes_item->identifier;
                            $item_url = $votes_item->url;
                            $entity_type_name = __('Custom Item', LIKEBTN_I18N_DOMAIN);
                        } else {
                            if ($entity_info['entity_name'] && $entity_info['entity_id']) {
                                $item_title = _likebtn_get_entity_title($entity_info['entity_name'], $entity_info['entity_id']);
                                $item_title = _likebtn_prepare_title($entity_info['entity_name'], $item_title);
                                $item_url = _likebtn_get_entity_url($entity_info['entity_name'], $entity_info['entity_id'], '', $votes_blog_id);
                            }
                            $entity_type_name = _likebtn_get_entity_name_title($entity_info['entity_name']);
                        }

                        if ((int)$votes_item->type == 1) {
                            $entity_vote_type = 'like';
                        } else {
                            $entity_vote_type = 'dislike';
                        }
                    ?>

                    <tr id="vote_<?php echo $votes_item->id; ?>">
                        <?php /*<td><input type="checkbox" class="item_checkbox" value="<?php echo $votes_item->post_id; ?>" name="item[]" <?php if ($blogs && $votes_item->blog_id != $blog_id): ?>disabled="disabled"<?php endif ?>></td>*/ ?>
                        <?php if ($blogs && $votes_blog_id == 'all'): ?>
                            <td><?php echo get_blog_option($votes_item->blog_id, 'blogname') ?></td>
                        <?php endif ?>
                        <?php if ($avatar_url): ?>
                            <td width="32">
                                <a href="<?php echo $user_url ?>" target="_blank"><img src="<?php echo $avatar_url; ?>" width="32" height="32" /></a>
                            </td>
                        <?php endif ?>
                        <td <?php if (!$avatar_url): ?>colspan="2"<?php endif ?>>
                            <?php if ($user_name): ?>
                                <a href="<?php echo $user_url ?>" target="_blank"><?php echo $user_name; ?></a>
                            <?php else: ?>
                                <?php echo __('Anonymous', LIKEBTN_I18N_DOMAIN); ?>
                            <?php endif ?>
                        </td>
                        <td><a href="javascript:likebtnIpInfo('<?php echo $votes_item->ip; ?>');" class="likebtn_ttip" title="<?php _e('View IP info', LIKEBTN_I18N_DOMAIN) ?>"><?php echo $votes_item->ip; ?></a></td>
                        <td><?php echo date("Y.m.d H:i:s", strtotime($votes_item->created_at)); ?></td>
                        <td>
                            <img src="<?php echo _likebtn_get_public_url()?>img/thumb/<?php echo $entity_vote_type; ?>.png" alt="<?php _e(ucfirst($entity_vote_type), LIKEBTN_I18N_DOMAIN) ?>" title="<?php _e(ucfirst($entity_vote_type), LIKEBTN_I18N_DOMAIN) ?>" class="likebtn_ttip" />
                        </td>
                        <td><a href="<?php echo $item_url ?>" target="_blank"><?php echo $item_title; ?></a> 
                            <?php if ($entity_type_name): ?>
                                — <?php echo $entity_type_name ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        </form>
        <?php if (count($votes) && $p->lastpage > 1): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php echo $p->show(); ?>
                </div>
            </div>
        <?php endif ?>

    </div>

    <div id="likebtn_ip_info" class="likebtn_ip_info hidden">
        <div class="likebtn_ip_info_map"></div>
        <table class="widefat">
            <tr>
                <th><strong>IP</strong></th>
                <td class="likebtn-ii-ip" width="50%"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Country', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-country"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('City', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-city"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Lat/Long', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-latlon"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Postal Code', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-postal"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Network', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-network"><img src="<?php echo $loader ?>" /></td>
            </tr>
            <tr>
                <th><strong><?php _e('Hostname', LIKEBTN_I18N_DOMAIN); ?></strong></th>
                <td class="likebtn-ii-hostname"><img src="<?php echo $loader ?>" /></td>
            </tr>
        </table>
        <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
            <div class="ui-dialog-buttonset">
                <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-close" role="button"><span class="ui-button-text"><?php _e('Close', LIKEBTN_I18N_DOMAIN); ?></span></button>
            </div>
        </div>
    </div>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?v=3.exp&callback=showMap">
    </script>
    <?php

    _likebtn_admin_footer();
}

// get SQL query for retrieving votes
function _likebtn_get_votes_sql($prefix, $query_where, $query_orderby, $query_limit, $query_select = 'SQL_CALC_FOUND_ROWS', $query_join = '')
{
    $query = "
         SELECT {$query_select}
            v.id,
            v.identifier,
            v.type,
            v.user_id,
            v.ip,
            v.created_at,
            i.ID as item_id,
            i.url
         FROM {$prefix}".LIKEBTN_TABLE_VOTE." v
         LEFT JOIN {$prefix}".LIKEBTN_TABLE_ITEM." i on i.identifier = v.identifier 
         {$query_join}
         WHERE
            1 = 1
            {$query_where}
         {$query_orderby}
         {$query_limit}";

    return $query;
}
