/**
 * CoolInput Plugin
 *
 * @version 2.1 (19/08/2013)
 * @requires jQuery v1.2.6+
 * @see http://remysharp.com/2007/01/25/jquery-tutorial-text-box-hints/
 *
 * Dual licensed under the MIT and GPLv3 Licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-3.0.html
 */
/***
 * Este plugin e para poter colocar uma hint nos campos de texto da pagina
 */
(function(a) {
	a.fn.coolinput = function(d) {
		var e = {
			useHtml5 : true,
			hint : null,
			source : "title",
			removeSource : true,
			blurClass : "blur",
			extraClass : false,
			clearOnSubmit : true,
			clearOnFocus : true,
			persistent : true
		};
		if (d && typeof d == "object") {
			a.extend(e, d)
		} else {
			e.hint = d
		}
		e.html5 = e.useHtml5 && ("placeholder" in document.createElement("input"));
		return this.each(function() {
			var k = a(this), j = e.hint || k.attr(e.source), i = e.blurClass;
			if (e.removeSource && !e.hint) {
				k.removeAttr(e.source)
			}
			function c() {
				if (k.val() == "") {
					k.val(j).addClass(i)
				}
			}

			function b() {
				if (k.val() == j && k.hasClass(i)) {
					k.val("").removeClass(i)
				}
			}

			if (j) {
				if (!e.html5) {
					if (e.persistent) {
						k.blur(c)
					}
					if (e.clearOnFocus) {
						k.focus(b)
					}
					if (e.clearOnSubmit) {
						k.parents("form:first").submit(b)
					}
					if (e.extraClass) {
						k.addClass(e.extraClass)
					}
					c()
				} else {
					k.attr("placeholder", j)
				}
			}
		})
	}
})(jQuery);
