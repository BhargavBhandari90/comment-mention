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
					} else if (users.name.includes( mentionText )) {
						return users.name + " (" + users.key + ")";
					} else if (users.user_nicename.includes( mentionText )) {
						return users.user_nicename + " (" + users.name + ")";
					}
				},
				selectTemplate: function(item) {
					if ( 'undefined' !== typeof( Comment_Mention.mention_by_fullname ) && '1' === Comment_Mention.mention_by_fullname ) {
						return '@' + item.original.name;  // Use `name` in textarea1
					} else {
						return '@' + item.original.key;  // Use `name` in textarea1
					}
				}
			}
		);

		tribute.attach( $( "#commentform textarea" ) );
		tribute.attach( $( ".bbp-topic-form form textarea" ) );
		tribute.attach( $( ".bbp-reply-form form textarea" ) );

		if ( $( '#main_comment' ).length > 0 ) {
			$( '#comment' ).on( 'tribute-replaced', cmt_mntn_sync_usernames );
			$( '#comment' ).on( 'input', cmt_mntn_sync_usernames );
		}

		function cmt_mntn_sync_usernames() {

			let content = $( '#comment' ).val();

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

			$( '#main_comment' ).val( content );
		}
	}
);
