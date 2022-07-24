import{d as e}from"./_rollupPluginBabelHelpers-dc18acc4.js";function styleInject(e,t){void 0===t&&(t={});var a=t.insertAt;if(e&&"undefined"!=typeof document){var l=document.head||document.getElementsByTagName("head")[0],s=document.createElement("style");s.type="text/css","top"===a&&l.firstChild?l.insertBefore(s,l.firstChild):l.appendChild(s),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(document.createTextNode(e))}}styleInject(".b-sd-labels__list{-webkit-align-items:flex-start;align-items:flex-start;display:-webkit-flex;display:flex;-webkit-flex-direction:var(--labels-direction);flex-direction:var(--labels-direction);-webkit-flex-wrap:wrap;flex-wrap:wrap;margin:0 -4px -4px 0;padding:0}.b-sd-labels__item{line-height:1.5;list-style:none;margin:0 4px 4px 0;pointer-events:all}.b-sd-labels__item .MuiTooltip-tooltip{text-align:left}.b-sd-labels__item .MuiTooltip-tooltip p{margin-bottom:0}");styleInject(".b-sd-label{background-color:var(--label-background-color);border-radius:var(--labels-border-radius);color:var(--label-color);cursor:default;font-size:var(--labels-font-size);padding:4px 8px;text-align:center}");var t=window.pwajet.core.components.Tooltip,a=window.pwajet.core.utils.appendClassName,l=function Label(l){var s=l.text,o=l.tooltip,r=l.style,n=l.className,i=window.React.useState(!1),d=e(i,2),p=d[0],b=d[1],c=window.React.createElement("div",{className:a("b-sd-label",n),style:r,__owner_data:{parentProps:l,name:"pwajet-sd-labels.label/Label"}},s);if(!o.length)return c;var w=window.React.createElement("div",{dangerouslySetInnerHTML:{__html:o},__owner_data:{parentProps:l,name:"pwajet-sd-labels.label/Label"}});return window.React.createElement(t,{title:w,open:p,onClose:function handleClose(){b(!1)},onOpen:function handleOpen(){b(!0)},__owner_data:{parentProps:l,name:"pwajet-sd-labels.label/Label"}},c)},s=function Labels(e){var t=e.labels,a=e.style;return t.length?window.React.createElement("div",{className:"b-sd-labels",style:a,__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels/Labels"}},window.React.createElement("ul",{className:"b-sd-labels__list",__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels/Labels"}},t.map((function(t){var a=t.id,s=t.name,o=t.tooltipContent,r=t.className,n=t.style;return window.React.createElement("li",{key:a,className:"b-sd-labels__item",__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels/Labels"}},window.React.createElement(l,{text:s,tooltip:o,className:r,style:{"--label-color":null==n?void 0:n.color,"--label-background-color":null==n?void 0:n.backgroundColor},__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels/Labels"}}))})))):null};styleInject(".b-product-single__image-wrapper{position:relative}.b-product-single__image-wrapper:hover .b-sd-labels-wrapper--hide-on-hover{opacity:0;pointer-events:none}.b-product-grid-item__body .b-sd-labels-wrapper{margin-bottom:4px}.b-product-grid-item:hover .b-sd-labels-wrapper--hide-on-hover{opacity:0}.b-sd-labels-wrapper--overlay{height:0;left:0;padding-bottom:100%;pointer-events:none;position:absolute;top:0;width:100%;z-index:5}.b-sd-labels-wrapper--overlay .b-sd-labels__list{position:absolute}.b-sd-labels-wrapper--bottom-right .b-sd-labels__list{bottom:0;right:0}.b-sd-labels-wrapper--bottom-left .b-sd-labels__list{bottom:0;left:0}.b-sd-labels-wrapper--top-left .b-sd-labels__list{left:0;top:0}.b-sd-labels-wrapper--top-right .b-sd-labels__list{right:0;top:0}.b-sd-labels-wrapper--bottom-right .b-sd-labels__list,.b-sd-labels-wrapper--top-right .b-sd-labels__list{-webkit-align-items:flex-end;align-items:flex-end;-webkit-justify-content:flex-end;justify-content:flex-end}.b-sd-labels-wrapper--hide-on-hover{opacity:var(--opacity-maximum);transition:opacity .15s ease-in-out}");var o=window.pwajet.core.utils.modifyClassName,r=window.reactRedux.connect((function mapStateToProps(e,t){var a=t.placement,l=e.Ui.properties.sdLabels;return{settings:null==l?void 0:l[a]}}),null)((function LabelsWrapper(e){var t=e.settings,a=window.React.useContext(window.pwajet.core.contexts.ProductContext);if(!t||!a)return null;var l=a.extra.labels;if(!l)return null;var r="shouldHideLabelsOnHover"in t&&t.shouldHideLabelsOnHover,n="position"in t&&t.position;return window.React.createElement("div",{className:o("b-sd-labels-wrapper",[n&&"overlay ".concat(n),r&&"hide-on-hover"]),__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels-wrapper/LabelsWrapper"}},window.React.createElement(s,{labels:l,style:{"--labels-font-size":"".concat(t.fontSize,"px"),"--labels-border-radius":"".concat(t.borderRadius,"px"),"--labels-direction":t.direction},__owner_data:{parentProps:e,name:"pwajet-sd-labels.labels-wrapper/LabelsWrapper"}}))}));export default r;
