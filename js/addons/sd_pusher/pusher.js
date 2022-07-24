(function(_, $) {
    var sd_pusher = new Pusher(_.sd_pusher_key, {
        authEndpoint: _.sd_pusher_endpoint + '&user_id=' + _.sd_pusher_user_id,
        auth: {
            headers: {
              'X-CSRF-Token': _.security_hash
            },
            params: {
                token: _.sd_pusher_auth_token
            }
        },
        cluster: _.sd_pusher_cluster,
        encrypted: _.sd_pusher_encrypted
    });

    var private_channel = sd_pusher.subscribe(_.sd_pusher_channel);

    private_channel.bind('pusher:subscription_error', function(status) {
        // no logic required
    });

    private_channel.bind('pusher:subscription_succeeded', function(status) {
        // no logic required
    });

    private_channel.bind(_.sd_pusher_event_notification, function(data) {
        if (data.message && data.message.notification) {
            notification = data.message.notification;

            $.ceAjax("request", _.sd_pusher_notify_url, {
                method: "post",
                data: {
                    type: notification.type,
                    message: notification.message,
                    message_state: notification.message_state,
                    variables: notification.variables,
                    lang_code: _.cart_language
                },
                hidden: true,
            });
        }
    });

    if (_.sd_pusher_presence_channel) {
	var presence_channel = sd_pusher.subscribe(_.sd_pusher_presence_channel);

        var fn_sd_pusher_is_valid_presence_object = function(presence_channel)
        {
            return presence_channel
                   && 'members' in presence_channel
                   && 'members' in presence_channel.members;
        };

        var fn_sd_pusher_update_active_users_quantity = function(presence_channel)
        {
            var fn_sd_pusher_show_admins_online_list = function(e)
            {
                var target = $(e.target);

                if (target.hasClass('active')) {
                    $('#sd_pusher_admins_online_list').html('');
                    target.removeClass('active');
                    return;
                }

                var can_view_all_profiles = false;

                if (fn_sd_pusher_is_valid_presence_object(presence_channel)) {
                    var my_id = presence_channel.members.myID;
                    var admins_list_html = '';

                    if (my_id
                        && presence_channel.members.members[presence_channel.members.myID]['company_id'] === "0") {
                        var can_view_all_profiles = true;
                    }

                    for (member in presence_channel.members.members) {
                        if (member !== my_id) {
                            var name = String(presence_channel.members.members[member]['name']).trim();
                            name = name ? name : _.sd_pusher_administrator;

                            admins_list_html += '<span class="admin-online-list-item">';

                            if (can_view_all_profiles
                                || (my_id
                                    && presence_channel.members.members[presence_channel.members.myID]['company_id']
                                        == presence_channel.members.members[member]['company_id']
                                )
                            ) {
                                var admin_page_url = fn_url('profiles.update&user_id=' + member);
                                admins_list_html += '<a target="blank" href="' + admin_page_url + '">' + name + '</a>';
                            } else {
                                admins_list_html += name;
                            }

                            admins_list_html += '</span>';
                        }

                        $('#sd_pusher_admins_online_list').html(admins_list_html);
                        target.addClass('active');
                    }
                }
            };

            if (fn_sd_pusher_is_valid_presence_object(presence_channel)) {
                var users_quantity = 0;
                var my_id = presence_channel.members.myID;
                var show_admins_list_button_id = "sd_pusher_show_admins_online_list_button";

                for (member in presence_channel.members.members) {
                    if (member !== my_id) {
                        users_quantity++;
                    }
                }

                if (users_quantity) {
                    $('.admins_online_icon').removeClass('sd-pusher-green').addClass('sd-pusher-red');

                    users_quantity = '<a href="javascript:void(0)" id="' + show_admins_list_button_id + '"> ' + users_quantity + '</a>';
                } else {
                    $('.admins_online_icon').removeClass('sd-pusher-red').addClass('sd-pusher-green');
                }

                $('#sd_pusher_admins_online_quantity').html(users_quantity);
                $('#sd_pusher_admins_online_list').html('');
                $('#' + show_admins_list_button_id).off();

                if (users_quantity) {
                    $('#' + show_admins_list_button_id).on('click', fn_sd_pusher_show_admins_online_list).addClass('active').click();
                }
            }
        };

	presence_channel.bind('pusher:subscription_error', function() {
	    // no logic required
	});

	presence_channel.bind('pusher:subscription_succeeded', function() {
            fn_sd_pusher_update_active_users_quantity(presence_channel);
	    // no logic required
	});

	presence_channel.bind('pusher:member_added', function() {
            fn_sd_pusher_update_active_users_quantity(presence_channel);
	});

	presence_channel.bind('pusher:member_removed', function() {
            fn_sd_pusher_update_active_users_quantity(presence_channel);
	});
    }
}(Tygh, Tygh.$));
