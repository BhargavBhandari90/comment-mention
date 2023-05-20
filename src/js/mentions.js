jQuery(function ($) {
	let getUsers;
	let tribute = new Tribute({
		values: function (search, cb) {
			getUsernames(search, (users) => cb(users));
		},
		lookup: function (users, mentionText) {
			if (users.key.includes(mentionText)) {
				return users.key + " (" + users.name + ")";
			} else if (users.name.includes(mentionText)) {
				return users.name + " (" + users.key + ")";
			} else if (users.user_nicename.includes(mentionText)) {
				return users.user_nicename + " (" + users.name + ")";
			}
		},
		iframe: tinyMCE,
	});

	tribute.attach($("#commentform textarea"));
	tribute.attach($(".bbp-topic-form form textarea"));
	tribute.attach($(".bbp-reply-form form textarea"));
	window.onload = function () {
		setTimeout(function () {
			cmt_mntn_tinymce();
		}, 1000);
		function cmt_mntn_tinymce() {
			if (
				typeof window.tinyMCE !== "undefined" &&
				window.tinyMCE.activeEditor !== null &&
				typeof window.tinyMCE.activeEditor !== "undefined"
			) {
				$(window.tinymce.activeEditor.contentDocument.activeElement);
			}
		}
	};

	function getUsernames(search, cb) {
		let data = {
			action: "cmt_mntn_get_users",
			term: search,
		};

		getUsers = $.ajax({
			url: Comment_Mention.ajaxurl,
			data: data,
			method: "GET",
			beforeSend: function () {
				if (getUsers != null) {
					getUsers.abort();
				}
			},
			success: function (response) {
				var usernames = response.data;
				cb(usernames);
			},
		});
	}
});
