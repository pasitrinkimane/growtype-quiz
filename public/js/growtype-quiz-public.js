!function(){var t,e={807:function(t,e,n){"use strict";function i(){return window.growtype_quiz_data}function r(){$(".growtype-quiz-progressbar").hide(),$(".growtype-quiz-header").hide(),$(".growtype-quiz-nav").hide(),$(".growtype-quiz-timer").length>0&&($(".growtype-quiz-timer").hide(),clearInterval(window.countdown_timer))}function a(){$(".growtype-quiz-header").show(),$(".growtype-quiz-nav").show(),$(".growtype-quiz-timer").length>0&&$(".growtype-quiz-timer").show()}function o(t){"true"===t.attr("data-hide-footer")&&r(),$(".growtype-quiz-nav .growtype-quiz-btn-go-back").show(),"true"===t.attr("data-hide-back-button")&&$(".growtype-quiz-nav .growtype-quiz-btn-go-back").hide(),"true"===t.attr("data-hide-next-button")?$(".growtype-quiz-nav .growtype-quiz-btn-go-next").hide():$(".growtype-quiz-nav .growtype-quiz-btn-go-next").show(),"true"===t.attr("data-hide-progressbar")?$(".growtype-quiz-progressbar").fadeOut(200):$(".growtype-quiz-progressbar").fadeIn(),$("body").attr("data-current-question-type",t.attr("data-question-type")).attr("data-current-answer-type",t.attr("data-answer-type"))}function s(t,e){var n="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!n){if(Array.isArray(t)||(n=function(t,e){if(!t)return;if("string"==typeof t)return u(t,e);var n=Object.prototype.toString.call(t).slice(8,-1);"Object"===n&&t.constructor&&(n=t.constructor.name);if("Map"===n||"Set"===n)return Array.from(t);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return u(t,e)}(t))||e&&t&&"number"==typeof t.length){n&&(t=n);var i=0,r=function(){};return{s:r,n:function(){return i>=t.length?{done:!0}:{done:!1,value:t[i++]}},e:function(t){throw t},f:r}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var a,o=!0,s=!1;return{s:function(){n=n.call(t)},n:function(){var t=n.next();return o=t.done,t},e:function(t){s=!0,a=t},f:function(){try{o||null==n.return||n.return()}finally{if(s)throw a}}}}function u(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,i=new Array(e);n<e;n++)i[n]=t[n];return i}window.growtype_quiz_data||(window.growtype_quiz_data=new Event("saveQuizData"),window.growtype_quiz_data.answers=null===sessionStorage.getItem("growtype_quiz_answers")?{}:JSON.parse(sessionStorage.getItem("growtype_quiz_answers")),window.growtype_quiz_data.correctlyAnswered={},window.growtype_quiz_data.extra_details={}),document.addEventListener("saveQuizData",(function(t){var e,n;if("false"===growtype_quiz_local.save_answers)return!1;var i=t.answers&&Object.entries(t.answers).length>0?t.answers:null,r=t.extra_details&&Object.entries(t.extra_details).length>0?t.extra_details:null,a=null!==(e=window.growtype_quiz_global.duration)&&void 0!==e?e:null,o=$(".growtype-quiz-wrapper").attr("data-quiz-id"),u=null!==(n=window.growtype_quiz_global.files)&&void 0!==n?n:null,l=new FormData;l.append("action","growtype_quiz_save_data"),l.append("answers",JSON.stringify(i)),r&&l.append("extra_details",JSON.stringify(r));if(l.append("quiz_id",o),l.append("duration",a),l.append("unique_hash",window.growtype_quiz_local.unique_hash),u){var w,d=s(u.entries());try{for(d.s();!(w=d.n()).done;){var p=w.value;l.append(p[0],p[1])}}catch(t){d.e(t)}finally{d.f()}}$.ajax({url:growtype_quiz_local.ajax_url,type:"post",processData:!1,contentType:!1,cache:!1,enctype:"multipart/form-data",data:l,beforeSend:function(){},success:function(t){t.success&&($('input[name="growtype_quiz_unique_hash"]').val(t.unique_hash),localStorage.setItem("quiz_answers",JSON.stringify(i)),null!==t.redirect_url&&t.redirect_url.length>0&&window.location.replace(t.redirect_url),$(".growtype-quiz-loader-wrapper").length>0&&($(".growtype-quiz-loader-wrapper").attr("data-redirect-url",t.results_url),$(".growtype-quiz-loader-wrapper .btn-continue").attr("href",t.results_url)))},error:function(t){t.responseJSON&&void 0!==t.responseJSON.message&&console.error(t.responseJSON.message),"true"!==$(".growtype-quiz-question.is-active").attr("data-hide-back-button")&&$(".growtype-quiz-wrapper .btn").attr("disabled",!1).fadeIn()},complete:function(){}})}));function l(){var t=window.quizQuestionsAmount<10?"0"+window.quizQuestionsAmount:window.quizQuestionsAmount,e=window.growtype_quiz_global.current_question_counter_nr<10?"0"+window.growtype_quiz_global.current_question_counter_nr:window.growtype_quiz_global.current_question_counter_nr;"steps"!==$(".growtype-quiz-question-nr").attr("data-counter-style")&&"outof"!==$(".growtype-quiz-question-nr").attr("data-counter-style")||(t=window.quizQuestionsAmount,e=window.growtype_quiz_global.current_question_counter_nr),"answered_only"===$(".growtype-quiz-question-nr").attr("data-counter-style")&&(t=window.quizQuestionsAmount,e=window.growtype_quiz_global.current_question_counter_nr-1),$(".growtype-quiz-question-nr .growtype-quiz-question-nr-current-slide").text(e),$(".growtype-quiz-question-nr .growtype-quiz-question-nr-total-slide").text(t),$("body").attr("data-current-question",window.growtype_quiz_global.current_question_nr)}function w(){if(growtype_quiz_local.show_question_nr_in_url){var t=new URLSearchParams(window.location.search);t.set("question",window.growtype_quiz_global.current_question_nr);var e=window.location.protocol+"//"+window.location.host+window.location.pathname+"?"+t.toString();window.history.pushState({path:e},"",e)}window.quizQuestionsAmount=$('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length,window.quizCountedQuestionsAmount=$('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.growtype-quiz-question[data-question-type="info"]):not([class*="skipped"]):not(.is-always-visible)').length+window.growtype_quiz_global.additional_questions_amount,window.growtype_quiz_global.already_visited_questions_funnels.map((function(t){if(t!==window.growtype_quiz_global.initial_funnel){var e=$('.growtype-quiz-question[data-funnel="'+t+'"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;window.quizQuestionsAmount=window.quizQuestionsAmount+e}})),$(".growtype-quiz-question:visible").hasClass("is-visible")&&window.quizQuestionsAmount--}var d=0,p=0;function c(){var t=$(".growtype-quiz-progressbar"),e=window.quizCountedQuestionsAmount,n=window.growtype_quiz_global.current_question_counter_nr-1,i=$(".growtype-quiz-question.chapter-start").length;0!==t.length&&0!==n||$(".growtype-quiz-progressbar-inner").width(0);var r=t.width();if(i>0){i+=1,$(".growtype-quiz-progressbar .growtype-quiz-progressbar-chapter").remove();var a=0,o=[];$(".growtype-quiz .growtype-quiz-question").each((function(t,e){"info"===$(e).attr("data-question-type")||$(e).attr("data-funnel")!==window.growtype_quiz_global.initial_funnel&&!$(e).hasClass("is-conditionally-cloned")||a++,$(e).hasClass("chapter-start")&&(0===o.length?o.push({chapter_start:0,chapter_end:a,steps_difference:a}):o.push({chapter_start:o[o.length-1].chapter_end,chapter_end:a,steps_difference:a-o[o.length-1].chapter_end}))})),o.push({chapter_start:o[o.length-1].chapter_end,chapter_end:e,steps_difference:e-o[o.length-1].chapter_end});for(var s=r/i,u=1;u<i;u++)$(".growtype-quiz-progressbar").append('<span class="growtype-quiz-progressbar-chapter" style="left:'+s*u+'px;"></span>');var l=0;o.map((function(t,e){if(t.chapter_start<n){t.chapter_end-n==0||t.chapter_end;(p=s/t.steps_difference*(n-t.chapter_start))>s&&(p=s),l+=p}})),d=l}else d=n*(p=r/e);$(".growtype-quiz-progressbar-inner").width(d),sessionStorage.setItem("growtype_quiz_global",JSON.stringify(window.growtype_quiz_global))}var g=new Event("growtypeQuizShowSuccessQuestion");function q(){return g}function y(t){var e=!0;return t.find("input[required]").each((function(t,n){var i;(0===$(n).val().length||"email"===$(n).attr("type")&&(i=$(n).val(),!String(i).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)))&&(e=!1)})),e}function f(){var t=!0;$(".growtype-quiz-question:visible").each((function(e,n){var i=$(n);(t=y(i))&&("radio"===i.attr("data-question-type")?t=function(t){var e=!0,n=t.find(".growtype-quiz-question-answer.is-active");if(0===n.length&&(e=!1),e&&"scored"===$(".growtype-quiz-wrapper").attr("data-quiz-type")&&growtype_quiz_local.show_correct_answer&&"on_submit"===growtype_quiz_local.correct_answer_trigger){n.map((function(t,n){"1"!==$(n).attr("data-cor")&&(e=!1,$(n).addClass("is-wrong"))}));var i=!0;t.find('.growtype-quiz-question-answer[data-cor="1"]').map((function(t,n){$(n).hasClass("is-active")||(e=!1,i=!1)})),i&&(e=!0,t.find(".growtype-quiz-question-answer").removeClass("is-wrong"),t.find('.growtype-quiz-question-answer[data-cor="1"]').addClass("is-correct"),t.find(".growtype-quiz-question-answer.is-wrong").removeClass("is-active"))}return e||t.attr("data-hint")&&t.find(".growtype-quiz-hint").fadeIn(),e}(i):"open"===i.attr("data-question-type")?t=function(t){var e=!0;return 0===t.find("textarea").val().length&&(e=!1),e}(i):"general"===i.attr("data-question-type")&&(t=function(t){var e=!0,n=t.find(".growtype-quiz-question-answer"),i=t.find(".growtype-quiz-question-answer.is-active");return n.length>0&&0===i.length&&(e=!1),e}(i)));var r=$(n).find('input[type="checkbox"]');if(r.length>0&&r.each((function(e,n){$(n).is(":checked")||(t=!1)})),$(".growtype-quiz-wrapper").removeClass("is-valid is-half-valid"),void 0!==$(n).find('input:not([type="checkbox"])').val()&&$(n).find('input:not([type="checkbox"])').val().length>0?$(".growtype-quiz-wrapper").addClass(t?"is-valid":"is-half-valid"):$(".growtype-quiz-wrapper").addClass(t?"is-valid":""),!t)return $(n).find(".growtype-quiz-question-answers").addClass("anim-wrong-selection"),setTimeout((function(){$(n).find(".growtype-quiz-question-answers").removeClass("anim-wrong-selection")}),500),!1})),window.growtype_quiz_global.is_valid=t}function _(t){var e=i().answers,n=i().correctlyAnswered,r=t.attr("data-key"),a=t.attr("data-question-type");if("info"!==a){if(e[r]=[],"open"===a?e[r].push(t.find("textarea").val()):t.find(".growtype-quiz-question-answer.is-active").map((function(t,n){e[r].push($(this).attr("data-value"))})),$('.growtype-quiz-wrapper[data-quiz-type="scored"]').length>0&&"open"!==a){var o=!0;t.find(".growtype-quiz-question-answer").map((function(t,e){$(this).hasClass("is-active")&&0===$(this).attr("data-cor").length&&(o=!1)})),n[r]||(n[r]=[]),n[r].push(o)}if(t.find("input[required]").length>0){var s=new FormData;t.find("input[required]").each((function(t,n){"file"===$(n).attr("type")?(s.append($(n).attr("name")+"-"+r+"-"+t,$(n)[0].files[0]),window.growtype_quiz_global.files=s):e[r].push({name:$(n).attr("name"),value:$(n).val()})}))}var u=!1;Object.entries(e).map((function(t){u&&delete e[t[0]],t[0]===r&&(u=!0)})),sessionStorage.setItem("growtype_quiz_answers",JSON.stringify(e)),i().answers=e}}function z(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}document.addEventListener("validateQuestion",f);var v=function(){function t(){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t)}var e,n,i;return e=t,(n=[{key:"clickInit",value:function(t){var e=t.closest(".growtype-quiz-question").attr("data-answers-limit"),n=t.closest(".growtype-quiz-question").find(".growtype-quiz-question-answer.is-active").length;t.attr("data-url").length>0&&(window.location=t.attr("data-url"));var i=t.closest(".growtype-quiz-question").attr("data-answer-type");if("multiple"!==i&&t.closest(".growtype-quiz-question-answers").find(".growtype-quiz-question-answer").removeClass("is-active"),t.hasClass("is-active"))"multiple"===i&&t.removeClass("is-active");else{if(e>0&&parseInt(n)===parseInt(e))return;t.addClass("is-active")}if("single_instant"===i){var r=t.closest(".growtype-quiz-question");return _(r),void h(r)}"multiple"===i&&f()}},{key:"init",value:function(){$(".growtype-quiz-question-answers .growtype-quiz-question-answer").click((function(){(new t).clickInit($(this))})),$('.growtype-quiz-question-answers .growtype-quiz-question-answer[data-option-featured-img-main="true"]').click((function(){var t=$(this).attr("data-img-url"),e=$(this).closest(".growtype-quiz-question").find(".b-img .e-img"),n=e.css("background-image").replace(/^url\(['"](.+)['"]\)/,"$1");t.length>0&&n!==t&&e.fadeOut(100).promise().done((function(){e.css({"background-image":"url( "+t+" )"}),e.fadeIn(100)}))}))}}])&&z(e.prototype,n),i&&z(e,i),Object.defineProperty(e,"prototype",{writable:!1}),t}();function h(t){var e=t.find(".growtype-quiz-question-answer.is-active").attr("data-funnel"),n=0;if(window.growtype_quiz_global.current_funnel=e,"multiple"===t.attr("data-answer-type")&&"true"===t.attr("data-has-funnel")){var r=[],s=0;if(t.find(".growtype-quiz-question-answer.is-active").map((function(t,e){$(e).attr("data-funnel")!==window.growtype_quiz_global.initial_funnel&&r.push($(e).attr("data-funnel"))})),r.length>0){var u=[];r.map((function(e,n){var i=t.nextAll('.growtype-quiz-question[data-has-funnel="true"][data-funnel-conditional="'+e+'"]');0!==i.length&&(s+=i.length,i.map((function(t,a){var o=e;i.length===t+1&&(r.length===n+1?(o=window.growtype_quiz_global.initial_funnel,console.info("Last same funnel conditional question has answer with next funnel - "+o)):o=r[n+1]);var s=$(a).clone().addClass("is-conditionally-cloned");$(a).addClass("is-conditionally-skipped"),$(s).attr("data-funnel",e),$(s).find(".growtype-quiz-question-answer").attr("data-funnel",o),s.find(".growtype-quiz-question-answer").click((function(){(new v).clickInit($(this))})),u[e]?u[e].push(s):u[e]=[s]})))}));var d=[];Object.entries(u).map((function(t){t[1].map((function(t){d.push(t)}))})),d.length>0&&(e=d[0].attr("data-funnel")),d.reverse(),d.map((function(e){$(e).insertAfter(t)}))}window.growtype_quiz_global.additional_questions_amount=s}void 0===e&&(e=window.growtype_quiz_global.initial_funnel);var p=t.nextAll('.growtype-quiz-question[data-funnel="'+e+'"]:not([class*="skipped"]):first');if(p.attr("data-disabled-if")&&p.attr("data-disabled-if").length>0)for(var g=t.nextAll('.growtype-quiz-question[data-funnel="'+e+'"]:not([class*="skipped"])'),y=0;y<g.length;y++){var _=g[y],z=$(_).attr("data-disabled-if");if(!(z.length>0)){p=$(_),console.warn('Nex question "Disabled If" value is empty, so next question is taken.');break}if("break"===function(){var t=z.split(":")[0],e=z.split(":")[1].split("|");if(void 0===window.growtype_quiz_data.answers[t])return console.warn("Question key not found among answers"),p=$(_),"break";var n=!1;if(e.map((function(e){window.growtype_quiz_data.answers[t].includes(e)&&(n=!0)})),!n)return console.warn("Question found among answers but ignored answers not found."),p=$(_),"break";console.warn("Question  "+y+"  is skipped.")}())break}window.growtype_quiz_global.already_visited_questions_keys.push(t.attr("data-key")),window.growtype_quiz_global.already_visited_questions_funnels.push(t.attr("data-funnel")),window.quizLastQuestion=t,"info"!==$(t).attr("data-question-type")&&window.growtype_quiz_global.current_question_counter_nr++,window.growtype_quiz_global.current_question_nr=p.attr("data-question-nr"),a(),growtype_quiz_local.show_correct_answer&&"after_submit"===growtype_quiz_local.correct_answer_trigger&&(n=1e3,t.find(".growtype-quiz-question-answer").map((function(t,e){"1"!==$(e).attr("data-cor")?$(e).addClass("is-wrong"):$(e).addClass("is-correct")}))),t.delay(n).removeClass("is-active").not(".is-always-visible").fadeOut(300,(function(){$(".growtype-quiz-wrapper").removeClass("is-valid is-half-valid")})).promise().done((function(){if(!window.growtype_quiz_global.is_finished){var n=$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label-finish");if(parseInt(window.growtype_quiz_global.current_question_nr)===parseInt(window.quizQuestionsAmount)&&n.length>0&&$(this).closest(".growtype-quiz").find(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").text(n),window.growtype_quiz_global.current_question_nr<window.quizQuestionsAmount-1){var r=$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label"),a=p.nextAll(".growtype-quiz-question:first").attr("data-question-title");p.nextAll('.growtype-quiz-question[data-funnel="'+e+'"]:first').length>0&&(a=p.nextAll('.growtype-quiz-question[data-funnel="'+e+'"]:first').attr("data-question-title")),"true"===$(".growtype-quiz-nav").attr("data-question-title-nav")&&a.length>0&&(r=a),$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label",r).text(r)}p.length>0&&(w(),l(),c(),p.addClass("is-active").fadeIn(300).promise().done((function(){void 0!==p.find("input").val()&&p.find("input").val().length>0&&f(),$(".growtype-quiz-nav .btn").attr("disabled",!1)}))),o(p),0===p.length||"success"===p.attr("data-question-type")?($(".growtype-quiz-btn-go-back").attr("disabled",!1).hide(),$(".growtype-quiz-btn-go-next").hide(),document.dispatchEvent(i()),document.dispatchEvent(q())):document.dispatchEvent(new CustomEvent("growtypeQuizShowNextQuestion",{detail:{currentQuestion:t,nextQuestion:p}}))}}))}var b=new Event("validateQuestion");function m(){var t=$(".growtype-quiz-question.first-question");$(".growtype-quiz .growtype-quiz-btn-go-next").click((function(){var e=$(".growtype-quiz-question.is-active");0===e.length&&(e=t),("false"===e.attr("data-answer-required")||(document.dispatchEvent(b),window.growtype_quiz_global.is_valid))&&($(".growtype-quiz-nav .btn").attr("disabled",!0),$(".growtype-quiz-question.is-visible").length>0&&$(".growtype-quiz-question.is-visible").each((function(t,e){_($(e))})),_(e),h(e))}))}function x(t,e){w(),l(),c(),$(".growtype-quiz-btn-go-next").show().attr("disabled",!1);var n=$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label");e.hasClass("first-question")&&(n=$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label-start"));var i=t.attr("data-question-title");"true"===$(".growtype-quiz-nav").attr("data-question-title-nav")&&i.length>0&&(n=i),o(e),window.growtype_quiz_global.current_question_nr<window.quizQuestionsAmount-1&&e.closest(".growtype-quiz").find(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").text(n),e.addClass("is-active").fadeIn(300).promise().done((function(){window.quizBackBtnWasClicked=!1}))}function k(){$(".growtype-quiz-btn-go-back").click((function(){return event.preventDefault(),!window.quizBackBtnWasClicked&&(window.quizBackBtnWasClicked=!0,0===window.growtype_quiz_global.already_visited_questions_keys.length?window.location.href="/":(t=$(".growtype-quiz-question.is-active"),e=window.growtype_quiz_global.already_visited_questions_keys.slice(-1)[0],n=window.growtype_quiz_global.already_visited_questions_funnels.slice(-1)[0],"multiple"===(r=t.prevAll(".growtype-quiz-question[data-key='"+e+"'][data-funnel='"+n+"']:first")).attr("data-answer-type")&&"true"===r.attr("data-has-funnel")&&(window.growtype_quiz_global.additional_questions_amount=0,$(".growtype-quiz-question.is-conditionally-cloned").remove(),$(".growtype-quiz-question.is-conditionally-skipped").removeClass("is-conditionally-skipped")),0===t.length&&(r=$(".growtype-quiz-question:last")),window.growtype_quiz_global.already_visited_questions_keys.splice(-1),window.growtype_quiz_global.already_visited_questions_funnels.splice(-1),delete i().answers[e],sessionStorage.setItem("growtype_quiz_answers",JSON.stringify(i().answers)),window.quizLastQuestion=t,window.growtype_quiz_global.current_question_nr=r.attr("data-question-nr"),"info"!==r.attr("data-question-type")&&window.growtype_quiz_global.current_question_counter_nr--,void(0===t.length?x(t,r):t.removeClass("is-active").fadeOut(300,(function(){$(".growtype-quiz-wrapper").addClass("is-valid"),x(t,r)})))));var t,e,n,r}))}window.quizBackBtnWasClicked=!1;var C=new Date;var A=new Event("growtypeQuizResultsEvaluated");function O(){return A}function S(){$(".growtype-quiz .btn-restart-quiz").click((function(t){var e;t.preventDefault(),window.growtype_quiz_global.is_finished=!1,window.growtype_quiz_global.was_restarted=!0,growtype_quiz_local.show_correct_answer&&"on_restart"===growtype_quiz_local.correct_answer_trigger&&$(".growtype-quiz-question").map((function(t,e){$(e).find(".growtype-quiz-question-answer").map((function(t,e){"1"!==$(e).attr("data-cor")?$(e).addClass("is-wrong"):$(e).addClass("is-correct")}))})),$(".growtype-quiz-nav .btn").attr("disabled",!1),(e=$(".growtype-quiz-question.first-question")).hasClass("is-always-visible")&&(e=$(".growtype-quiz-question:not(.is-always-visible):first")),o(e),setTimeout((function(){var t=$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label-start");void 0!==t&&t.length>0&&("true"===$(".growtype-quiz-nav").attr("data-question-title-nav")?(t=e.nextAll(".growtype-quiz-question:first").attr("data-question-title"),$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").attr("data-label",t).text(t)):$(".growtype-quiz-nav .growtype-quiz-btn-go-next .e-label").text(t))}),500),e.hasClass("is-active")||$(".growtype-quiz-question").removeClass("is-active").fadeOut().promise().done((function(){window.growtype_quiz_global.current_question_nr=1,window.growtype_quiz_global.current_question_counter_nr=1,l(),e.addClass("is-active").fadeIn(),a(),$(".growtype-quiz-btn-go-next").show()}))}))}var Q=new Event("loaderFinished");function I(){if($(".growtype-quiz-loader-wrapper").length>0){var t=function(){$(".growtype-quiz-loader-percentage").html(n+"<span>%</span>"),e()},e=function(){n<100?(n++,setTimeout(t,i)):document.dispatchEvent(Q)},n=0,i=$(".growtype-quiz-loader-wrapper").attr("data-duration");t()}}document.addEventListener("growtypeQuizShowSuccessQuestion",(function(){window.growtype_quiz_global.is_finished=!0,r(),function(){var t={answers:i().answers,correctlyAnswered:i().correctlyAnswered,totalAnswers:Object.values(i().correctlyAnswered).length,correctAnswers:0};Object.values(t.correctlyAnswered).map((function(e,n){e[0]&&t.correctAnswers++})),t.correctAnswersFormatted=t.correctAnswers,Object.values(t.correctlyAnswered).length>=10&&t.correctAnswers.length<10&&(t.correctAnswersFormatted="0"+t.correctAnswers);var e=t.correctAnswersFormatted+"/"+t.totalAnswers;$('.growtype-quiz-question[data-question-type="success"] .e-result').length>0&&$('.growtype-quiz-question[data-question-type="success"] .e-result').text(e),O().results=t,document.dispatchEvent(O())}(),$(".growtype-quiz-question").removeClass("is-active").hide().promise().done((function(){$("body").attr("data-current-question-type","success"),$('.growtype-quiz-question[data-question-type="success"]').fadeIn(),I(),S()}))}));n(927);$=jQuery,$(document).ready((function(){window.growtype_quiz_global&&(growtype_quiz_local.save_data_on_load&&document.dispatchEvent(i()),(new v).init(),$(".growtype-quiz input[type=file]").change((function(t){var e=$(this).attr("max-size"),n=$(this).attr("max-size-error-message");void 0!==e&&$(t.target.files).each((function(i,r){r.size>e&&(n.length>0?(n=n.replace(":image_name",r.name).replace(":max_size",e/1e6+"mb"),alert(n)):alert(r.name+" is too big! Max file size allowed - "+e/1e6+"mb"),t.target.value="")}));var i=$(this).attr("data-selected-placeholder-single"),r=$(this).attr("data-selected-placeholder-multiple"),a=t.target.files.length;if($(this).closest(".growtype-quiz-input-wrapper").find(".growtype-quiz-input-label").removeClass("is-active").text($(this).attr("data-placeholder")),a>0&&(i.length>0||r.length>0)){var o=i.replace(":nr",a);a>1&&(o=r.replace(":nr",a)),$(this).closest(".growtype-quiz-input-wrapper").find(".growtype-quiz-input-label").addClass("is-active").text(o)}})),$(".growtype-quiz-input-wrapper input").on("keyup",(function(){f()})),$('.growtype-quiz-input-wrapper input[type="checkbox"]').on("click",(function(){f()})),function(){var t=new URLSearchParams(window.location.search).get("question");t=null===t||growtype_quiz_local.show_question_nr_in_url?parseInt(t):1;var e=$('.growtype-quiz-question[data-question-nr="'+t+'"]');t>1&&($(".growtype-quiz-question").hide(),window.growtype_quiz_global.current_question_nr=t),e.addClass("is-active").show(),o(e)}(),m(),k(),w(),c(),l(),function(){if("presentation"===$(".growtype-quiz-wrapper").attr("data-quiz-type"))return!1;setInterval((function(){var t=new Date-C;window.growtype_quiz_global.duration=(t/1e3).toFixed(0)}),1e3)}(),function(){var t=$(".growtype-quiz-timer");if(0===t.length)return!1;window.growtype_quiz_global.countdown={};var e=t.attr("data-duration"),n=new Date;n.setSeconds(n.getSeconds()+Number(e));var r=n.getTime();window.countdown_timer=setInterval((function(){var n=(new Date).getTime(),a=r-n,o=(Math.floor(a/864e5),Math.floor(a%864e5/36e5),Math.floor(a%36e5/6e4)),s=o,u=Math.floor(a%6e4/1e3),l=u;a<0?(clearInterval(window.countdown_timer),document.dispatchEvent(i()),document.dispatchEvent(q())):(window.growtype_quiz_global.countdown.duration=Number(e)-Number(60*o+u),o<10&&(s="0"+o),u<10&&(l="0"+u),window.growtype_quiz_global.countdown.current_time=s+":"+l,t.find(".e-time").text(window.growtype_quiz_global.countdown.current_time))}),1e3)}())}))},927:function(){document.addEventListener("loaderFinished",(function(){var t=$(".growtype-quiz-loader-wrapper").attr("data-redirect");$(".growtype-quiz-wrapper").addClass("is-valid"),"true"===t&&(window.location.href=$(".growtype-quiz-loader-wrapper").attr("data-redirect-url"))}))},530:function(){},216:function(){}},n={};function i(t){var r=n[t];if(void 0!==r)return r.exports;var a=n[t]={exports:{}};return e[t](a,a.exports,i),a.exports}i.m=e,t=[],i.O=function(e,n,r,a){if(!n){var o=1/0;for(w=0;w<t.length;w++){n=t[w][0],r=t[w][1],a=t[w][2];for(var s=!0,u=0;u<n.length;u++)(!1&a||o>=a)&&Object.keys(i.O).every((function(t){return i.O[t](n[u])}))?n.splice(u--,1):(s=!1,a<o&&(o=a));if(s){t.splice(w--,1);var l=r();void 0!==l&&(e=l)}}return e}a=a||0;for(var w=t.length;w>0&&t[w-1][2]>a;w--)t[w]=t[w-1];t[w]=[n,r,a]},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},function(){var t={172:0,697:0,412:0};i.O.j=function(e){return 0===t[e]};var e=function(e,n){var r,a,o=n[0],s=n[1],u=n[2],l=0;if(o.some((function(e){return 0!==t[e]}))){for(r in s)i.o(s,r)&&(i.m[r]=s[r]);if(u)var w=u(i)}for(e&&e(n);l<o.length;l++)a=o[l],i.o(t,a)&&t[a]&&t[a][0](),t[a]=0;return i.O(w)},n=self.webpackChunksage=self.webpackChunksage||[];n.forEach(e.bind(null,0)),n.push=e.bind(null,n.push.bind(n))}(),i.O(void 0,[697,412],(function(){return i(807)})),i.O(void 0,[697,412],(function(){return i(530)}));var r=i.O(void 0,[697,412],(function(){return i(216)}));r=i.O(r)}();
//# sourceMappingURL=growtype-quiz-public.js.map