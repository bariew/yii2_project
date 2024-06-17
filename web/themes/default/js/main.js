FormManager = {
    init : function () {
        $(document).ajaxStart(function () { FormManager.disable(); }).ajaxComplete(function(){ FormManager.enable(); }); //disable form submit while updating date fields with ajax - see /common/site/date
    },
    buttons : $("button:visible[type='submit']"),
    enable : function () { FormManager.buttons.removeAttr("disabled"); },
    disable : function () {
        FormManager.buttons.attr("disabled","disabled");
        setTimeout(function () { FormManager.enable(); }, 5000);
    }
}

FieldManager = {
    options : {},
    init : function () {
        $("[data-depends]").each(function(){
            var that = $(this);
            var dependencies = JSON.parse(JSON.stringify(that.data("depends")));
            FieldManager.toggleDependencies(that);
            for (var selector in dependencies) {
                $(document).on("change", selector, function(e){
                    FieldManager.toggleDependencies(that);
                });
            }
        });
        $("[data-depends]").css({opacity:"100%"})
    },
    isMatch : function (selector, value) {
        if (!$(selector).length) {
            return !Number.parseInt(value); // show if 0 and no element hide if >=1 and no element
        } else if ($(selector).is(":checkbox")) {
            if (($(selector).is(":checked") && value == 0) || (!$(selector).is(":checked") && value == 1)) {
                return false;
            }
        } else if (value === true) {
            return Boolean($(selector).val().length);
        } else if (value.toString() !== $(selector).val().toString()) {
            return false;
        }
        return true;
    },
    // hide inputs that depend on another input unselected values
    toggleDependencies : function(element) {
        var dependencies = JSON.parse(JSON.stringify(element.data("depends")));
        var show = 0;
        for (var selector in dependencies) {
            var values = dependencies[selector];
            var currentShow = 0;
            values = Array.isArray(values) ? values : [values];
            for (var i in values) {
                if (FieldManager.isMatch(selector, values[i])) {
                    currentShow = 1;
                }
            }
            show+=currentShow;
        }
        if (show === Object.keys(dependencies).length) {
            element.removeClass("d-none").css("display", "").removeAttr("disabled").find(":input").removeAttr("disabled");
        } else {
            element.each(function () { // adding d-none will not work if it also has d-inline class etc
                this.style.setProperty("display", "none", "important" );
            })
            element.attr("disabled", "disabled").find(":input").attr("disabled", "disabled");
        }
    },
    errorScroll : function () {
        if(typeof $(".has-error").first().offset() !== "undefined") { // scroll to input error
            $("html, body").animate({
                scrollTop: $(".has-error").first().offset().top-100
            }, 500);
        }
    }
};

DataManager = { // removes repeating select options from repeating selects
    init: function () {
        $("select[data-remove]").each(function() {
            DataManager.remove($(this).data("remove"), $(this).val());
            $(this).data("oldval", JSON.stringify($(this).val()));
        });
        $(document).on("change", "select[data-remove]", function () {
            $(this).data("oldval") || $(this).data("oldval", JSON.stringify($(this).val()));
            DataManager.add($(this).data("remove"), JSON.parse($(this).data("oldval")));
            DataManager.remove($(this).data("remove"), $(this).val());
            $(this).data("oldval", JSON.stringify($(this).val()));
        });
        $(document).on("remove", "select[data-remove]", function () {
            DataManager.add($(this).data("remove"), $(this).val());
        })
    },
    remove: function (selector, value) {
        $(document).find(selector).each(function () {
            value = Array.isArray(value) ? value : [value];
            for (let i in value) {
                $(this).find("option[value='"+value[i]+"']:not(:selected)").attr("disabled", "disabled").end().trigger("select2.change");
            }
        })
    },
    add: function (selector, value) {
        $(document).find(selector).each(function () {
            value = Array.isArray(value) ? value : [value];
            for (let i in value) {
                $(this).find("option[value='"+value+"']").removeAttr("disabled").end().trigger("select2.change");
            }
        })
    }
};

TextHelper = {
    charAnimate: function (el, color) { // change text color char by char
        if ($(el).find(".char-animate.active").length) {
            $(el).find(".char-animate.active").eq(0).css({"color":color}).removeClass("active");
            setTimeout(function() { TextHelper.charAnimate(el,color) }, 50)// go to the next active char
        }
        if ($(el).find(".char-animate").length) { // if already split, stop
            return;
        }
        let content = $(el).html().replace(/&nbsp;/gi, " ").replace("<br>", "<br/>").replace("&amp;", "&").split(""); // split content by chars
        let skip = 0;
        for (let i in content) {
            let char = content[i];
            if ((char === "<") && (skip === 0)) {  // skip inner html
                skip = 2;
            }
            if (!skip && (char !== " ")) {
                content[i] = "<span class='char-animate active'>"+char+"</span>"; //wrap the char to animate it later
            }
            if (char === ">") {
                skip--;
            }
        }
        el.html(content.join(""));
        TextHelper.charAnimate(el,color); // go to animate it all
    }
};

(function ($) {
    "use strict";
    FormManager.init();
    FieldManager.init();
    $(document).on("click", "a[data-remove]", function(){
        $.get($(this).attr("href"));
        $($(this).data("remove")).remove();
        return false;
    });
    $(document).on("click", "a.ajax-link", function(e){
        e.preventDefault(); e.stopPropagation();
        $.post($(this).attr("href"), $(this).data("post"));
        return false;
    });
    $(document).on("mousedown touchstart", ".modal-body a[target='_blank']", function (e) {
        e.stopPropagation(); window.open($(this).attr("href"), "_blank"); //modal target _blank is not working
    })
    $(document).on("keydown mousedown touchstart", "select.disabled, input.noedit", function (e) {
        e.preventDefault();
    })
    DataManager.init();
    window.onbeforeunload = function(e) {/**  Preventing double form submit. */
    FormManager.disable();
        setTimeout(function () {FormManager.enable();}, 10000);
    };
    $(document).ajaxStart(function () {
        $("body:not('.nowait')").addClass("wait");
    });
    $(document).ajaxComplete(function () {
        $("body").removeClass("wait");
    });
    $(document).ajaxComplete(function() {
        FieldManager.init()
    })
    $(document).on("change", "form.ajax-form :input", function(e) {
        let form = $(this).parents("form");
        let container = form.parents(".ajax-form-container").length ? form.parents(".ajax-form-container") : form;
        event.preventDefault();
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: new FormData(form.get(0)),
            processData: false,
            contentType: false,
            success: function (data) {
                mainAlert.flash("success", 1);
                data && container.replaceWith(data);
            }
        });
    })
    $(document).on("keyup", ".enOnly", function () {
        var val = $(this).val().replace(/[^\x00-\x7F]/g, "");
        $(this).val(val);
    });
    $(document).on("keyup", ".enOnly", function () {
        var val = $(this).val().replace(/[^\x00-\x7F]/g, "");
        $(this).val(val);
    });
    if (window.location.hash) {
        setTimeout(function () {
            var hash = window.location.hash;
            window.location.hash = "";
            window.location.hash = hash;
        }, 300);
    }
    FieldManager.errorScroll();
    $(document).on("afterValidate", "form", function() { FieldManager.errorScroll(); });

})(jQuery)

