(()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e,t){for(var a=0;a<t.length;a++){var r=t[a];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,n(r.key),r)}}function n(t){var n=function(t,n){if("object"!=e(t)||!t)return t;var a=t[Symbol.toPrimitive];if(void 0!==a){var r=a.call(t,n||"default");if("object"!=e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(t,"string");return"symbol"==e(n)?n:n+""}var a=function(){return e=function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.$modelWrapper=$("#openai-model-wrapper"),this.$spinTemplateWrapper=$("#spin-template-wrapper"),this.$promptTemplateWrapper=$("#prompt-template-wrapper"),this.$modelWrapper.length&&this.handleMultipleModels(),this.$spinTemplateWrapper.length&&Array.isArray($spinTemplates)&&this.handleMultiSpinTemplate(),this.$promptTemplateWrapper.length&&Array.isArray($promptTemplates)&&this.handleMultiPromptTemplate()},n=[{key:"handleMultipleModels",value:function(){var e=this.$modelWrapper.find("#add-model"),t=this.$modelWrapper.data("default"),n=this.$modelWrapper.data("models");n.length||(n=[""]);var a=function(){var n=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",a=$('<div class="d-flex mt-2 more-model align-items-center">\n          <input type="radio" name="ai_writer_openai_default_model" class="setting-selection-option default-model" value="'.concat(n,'" ').concat(n===t?"checked":"",'>\n          <input class="next-input item-model" placeholder="').concat(e.data("placeholder"),'" name="ai_writer_openai_models[]" value="').concat(n,'" />\n          <a class="btn btn-link text-danger"><i class="fas fa-minus"></i></a>\n        </div>'));e.before(a)};this.$modelWrapper.on("click",".more-model > a",(function(){$(this).parents(".more-model").remove(),$(".more-model").length||a()})),this.$modelWrapper.on("change",".more-model > input.item-model",(function(){var e=$(this).val();$(this).siblings(".default-model").val(e)})),e.on("click",(function(e){e.preventDefault(),a()})),n.forEach((function(e){a(e)}))}},{key:"handleMultiSpinTemplate",value:function(){this.handleMultiTemplate("spin")}},{key:"handleMultiPromptTemplate",value:function(){this.handleMultiTemplate("prompt")}},{key:"handleMultiTemplate",value:function(e){var t="spin"===e?this.$spinTemplateWrapper:this.$promptTemplateWrapper,n=t.find(".add-template"),a=$("spin"===e?"#spin-html-template":"#prompt-html-template").get(0),r=0,i=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"",l=$(a.innerHTML);l.find(".item-title").attr("name","ai_writer_".concat(e,"_template[").concat(r,"][title]")).val(t),l.find(".item-content").attr("name","ai_writer_".concat(e,"_template[").concat(r,"][content]")).val(i),r++,n.before(l)};t.on("click",".more-template .remove-template",(function(e){e.preventDefault(),$(this).parents(".more-template").remove(),t.find(".more-template").length||i()})),n.on("click",(function(e){e.preventDefault(),i()})),("spin"===e?$spinTemplates:$promptTemplates).forEach((function(e){var t=e.title,n=e.content;i(t,n)}))}}],n&&t(e.prototype,n),a&&t(e,a),Object.defineProperty(e,"prototype",{writable:!1}),e;var e,n,a}();$(document).ready((function(){new a}))})();