jQuery(
	function ($) {
		let getUsers;
		const mentionMap = {};
		let tribute      = new Tribute(
			{
				values: function (search, cb) {
					let data = {
						action: "cmt_mntn_get_users",
						term: search,
					};

					getUsers = $.ajax(
						{
							url: Comment_Mention.ajaxurl,  // Replace with your API endpoint
							method: 'GET',
							data: data,    // Send current text as the search term
							dataType: 'json',
							beforeSend: function () {
								if (getUsers != null) {
									getUsers.abort();
								}
							},
							success: function(response) {
								// Format the response for Tribute.js
								// const results = response.data;
								const results = response.data.map(
									function(item) {
										mentionMap[item.key] = item.name;
										return item;
									}
								);

								cb( results );  // Pass formatted results to Tribute.js
							},
							error: function(xhr, status, error) {
								console.error( "Error fetching data:", error );
								cb( [] );  // Pass empty array on error
							}
						}
					);
				},
				lookup: function (users, mentionText) {

					if (users.key.includes( mentionText )) {
						return users.key + " (" + users.name + ")";
					} else if (users.name.toLowerCase().includes( mentionText ) || users.name.includes( mentionText )) {
						return users.name + " (" + users.key + ")";
					} else if (users.user_nicename.includes( mentionText )) {
						return users.user_nicename + " (" + users.name + ")";
					}
				},
				selectTemplate: function(item) {
					if ( 'undefined' !== typeof( Comment_Mention.mention_by_fullname ) && '1' === Comment_Mention.mention_by_fullname ) {
						return '@' + item.original.name;
					} else {
						return '@' + item.original.key;
					}
				}
			}
		);

		tribute.attach( $( "#commentform textarea" ) );
		tribute.attach( $( ".bbp-topic-form form textarea" ) );
		tribute.attach( $( ".bbp-reply-form form textarea" ) );

		if ( $( '#main_comment' ).length > 0 ) {
			$( '#comment' ).on( 'tribute-replaced', function() {
				cmt_mntn_sync_usernames( 'comment', 'main_comment' );
			});
			$( '#comment' ).on( 'input', function() {
				cmt_mntn_sync_usernames( 'comment', 'main_comment' );
			});
		}

		if ( $( '#bbp_main_reply_content' ).length > 0 ) {
			$( '#bbp_reply_content' ).on( 'tribute-replaced', function() {
				cmt_mntn_sync_usernames( 'bbp_reply_content', 'bbp_main_reply_content' );
			});
			$( '#bbp_reply_content' ).on( 'input', function() {
				cmt_mntn_sync_usernames( 'bbp_reply_content', 'bbp_main_reply_content' );
			});
		}

		if ( $( '#bbp_main_topic_content' ).length > 0 ) {
			$( '#bbp_topic_content' ).on( 'tribute-replaced', function() {
				cmt_mntn_sync_usernames( 'bbp_topic_content', 'bbp_main_topic_content' );
			});
			$( '#bbp_topic_content' ).on( 'input', function() {
				cmt_mntn_sync_usernames( 'bbp_topic_content', 'bbp_main_topic_content' );
			});
		}

		function cmt_mntn_sync_usernames( default_id, main_id ) {

			let content = $( '#' + default_id ).val();

			let already_mentioned = localStorage.getItem('mentionMap');

			if ( '' !== already_mentioned ) {
				already_mentioned = JSON.parse(already_mentioned);
				$.each(already_mentioned, function(key, value) {
					mentionMap[key] = value;
				});
			}

			Object.keys( mentionMap ).forEach(
				name => {
					const nameMention     = '@' + mentionMap[name];         // Mention format in textarea1 (name)
					const usernameMention = '@' + name; // Mention format in textarea2 (username)
					content               = content.split( nameMention ).join( usernameMention );
				}
			);

			$( '#' + main_id ).val( content );
		}
	}
);
