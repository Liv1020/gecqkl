$(function() {
	$('.menus0 .li').each(function() {
		$('.menus0 .li').mouseover(function() {
			var index = $(this).index();
			$('.menus0 .bg').css('left', (index - 1) * 50 + '%');
			$('.menus0 .li').css('color', '#000');
			$(this).css('color', '#fff');
		});
		$('.menus0 .li').mouseout(function() {
			$('.menus0 .li').css('color', '#000');
			$('.menus0 .bg').css('left', '0');
			$('.menus0 .li').eq(0).css('color', '#fff');
		});
	})
	$('.menus1 li').each(function() {
		var flag = 0;
		var i = 0;
		$('.menus1 .li').mouseover(function() {
			var index = $(this).index();
			$('.menus1 .bg').css('left', (index - 1) * 50 + '%');
			$('.menus1 .li').css('color', '#000');
			$(this).css('color', '#fff');
			$('.menus1 .li').click(function() {
				$('.menus1 .bg').css('left', (index - 1) * 50 + '%');
				flag = (index - 1) * 50;
				i = $(this).index() - 1;
				$(this).css('color', '#fff');
			})
			$('.menus1 .li').mouseout(function() {
				$('.menus1 .bg').css('left', flag + '%');
				$('.menus1 .li').css('color', '#000');
				$('.menus1 .li').eq(i).css('color', '#fff');
			});
		});
	})
	$('.menus2 li').each(function() {
		var flag = 0;
		var i = 0;
		$('.menus2 .li').mouseover(function() {
			var index = $(this).index();
			$('.menus2 .bg').css('left', (index - 1) * 50 + '%');
			$('.menus2 .li').css('color', '#000');
			$(this).css('color', '#fff');
			$('.menus2 .li').click(function() {
				$('.menus2 .bg').css('left', (index - 1) * 50 + '%');
				flag = (index - 1) * 50;
				i = $(this).index() - 1;
				$(this).css('color', '#fff');
				$('.menus2 .menus-list').removeClass('show')
				$('.menus2 .menus-list').eq(index - 1).addClass('show')
				$('.tab1').removeClass('show')
				$('.tab1').eq(index - 1).addClass('show')
			})
			$('.menus2 li').mouseout(function() {
				$('.menus2 .bg').css('left', flag + '%');
				$('.menus2 .li').css('color', '#000');
				$('.menus2 .li').eq(i).css('color', '#fff');
			});
		});
	})
	$('.menus3 .li').each(function() {
		$('.menus3 li').mouseover(function() {
			var index = $(this).index();
			$('.menus3 .bg').css('left', (index - 1) * 50 + '%');
			$('.menus3 .li').css('color', '#000');
			$(this).css('color', '#fff');
			$('.tab2').removeClass('show')
			$('.tab2').eq(index - 1).addClass('show')
		});
	})
});
(function($) {
	$.extend({
		alertView: function(options) {
			var defaults = {
				showMask: true,
				title: "",
				msg: "",
			};
			var settings = {};
			if($.type(options) == "string") {
				settings = defaults;
				settings.msg = options;
			} else if($.type(options) == "object") {
				settings = $.extend(true, defaults, options);
			}
			if(!settings.buttons || settings.buttons.length == 0) {
				settings.buttons = [{
					title: "确定"
				}];
			}
			$.closeView();
			if(settings.showMask) {
				$("body").append('<div id="popup-dialog-mask" class="lodding-mask"></div>');
			}
			var popupDialog = $('<div id="popup-dialog" class="popup-dialog"></div>');
			if(settings.title) {
				popupDialog.append('<h3 id="popup-dialog-title">' + settings.title + '</h3>');
			}
			popupDialog.append('<p id="popup-dialog-msg" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.msg + '</p>');
			popupDialog.append('<p id="popup-dialog-cde" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.cde + '</p>');
			popupDialog.append('<p id="popup-dialog-dj" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.dj + '</p>');
			popupDialog.append('<p id="popup-dialog-sl" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.sl+ '</p>');
			popupDialog.append('<p id="popup-dialog-je" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.je+ '</p>');
			popupDialog.append('<hr id="popup-dialog-x" class="x ' + ((settings.title ? "" : "margin-top-15")) + '"></p>');
			popupDialog.append('<p id="popup-dialog-fx" class="message ' + ((settings.title ? "" : "margin-top-15")) + '">' + settings.fx+ '</p>');
			
			if(settings.input && $.type(settings.input) == 'object') {
				var html = "<input"
				$.each(settings.input, function(key, value) {
					html += ' ' + key + '="' + value + '"';
				})
				html += "/>";
				$(html).appendTo(popupDialog);
			}
			var btnSize = settings.buttons.length;
			if(btnSize == 2) {
				var buttonGroup = $('<div class="ui-grid-a group"></div>');
				$.each(settings.buttons, function(i, btnJson) {
					var color = btnJson.color ? 'style="color:' + btnJson.color + '"' : "";
					var button = $('<div class="ui-block-' + (i == 0 ? "a" : "b") + '" ' + color + '>' + btnJson.title + '</div>');
					addBtnEvent(button, btnJson);
					buttonGroup.append(button);
				});
				popupDialog.append(buttonGroup);
			} else {
				$.each(settings.buttons, function(i, btnJson) {
					var color = btnJson.color ? 'style="color:' + btnJson.color + '"' : "";
					var button = $('<div class="ui-grid-a' + (i == 0 ? " group" : "") + '" ' + color + '>' + btnJson.title + '</div>');
					addBtnEvent(button, btnJson);
					popupDialog.append(button);
				})
			}
			$("body").append(popupDialog);
		},
		closeView: function() {
			$(".popup-dialog, .lodding-mask").fadeOut(50, function() {
				$(this).remove();
			});
		}
	});

	function addBtnEvent(btn, btnJson) {
		btn.on("click", function() {
			if(btnJson.click && typeof(btnJson.click) == "function") {
				var content = $(this).closest("div.popup-dialog");
				var val = $.trim(content.find("input").val());
				btnJson.click(val, content);
			}
			if(!btnJson.hasOwnProperty("autoClose") || btnJson.autoClose != false) {
				$.closeView();
			}
		});
	}
})(jQuery);
jQuery(function() {
	document.body.addEventListener('touchstart', function() {});
});
