const j=p=>{let h,x,v,u=[];p("#google-places-wp-tabs").tabs();function w(e,s){{p(".google-places-wp-places-results-container .notice").fadeOut(r=>remove(r));let n=p(".google-places-wp-results-drawer"),l=p(".opener-chevron div"),d=`<div class="notice is-dismissible notice-error" style="max-width: 70rem;margin: 1rem auto;"><p>${e}</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Hide this error.</span></button></div>`;p(".google-places-wp-results tbody").empty(),p(".google-places-wp-places-results-container").append(d),n.addClass("open"),l.addClass("open")}}function E(e="all"){const s=()=>{p(".google-places-wp-places-results-container .notice").fadeOut(l=>{l&&remove(l)}),p(".google-places-wp-results-drawer").removeClass("open"),p(".opener-chevron div").removeClass("open")},n=()=>{p("#google_places_settings .notice").fadeOut(l=>remove(l))};e=="all"?(s(),n()):e=="places"?s():n()}function L(e){const n=e.formattedAddress.split(",");n.pop();const l=n.map(o=>o.trim());let d="",r="",c="",a="",t=[];return/\d/.test(l[0])||l.shift(),l.forEach(o=>{o.match(/^[A-Z]{2} \d{5}$/)?[c,a]=o.split(" "):c&&a&&!r?r=o:!c&&!a&&!r?/\d/.test(o)?d=o:r=o:t.push(o)}),!r&&t.length&&(r=t.join(" ")),[d,r,c,a]}async function T(e){let s=new google.maps.places.AutocompleteSessionToken;return e.sessionToken=s,e}async function S(e,s,n){await e.fetchFields({fields:["displayName","formattedAddress","location"]}),n.target.value=`${e.displayName} - ${e.formattedAddress}`;let l=L(e),d="google_places_field",r=[{id:`${d}_streetaddress`,val:l[0]},{id:`${d}_city`,val:`${l[1]}, ${l[2]} ${l[3]}`},{id:`${d}_latlong`,val:[e.location.lat(),e.location.lng()].join(",")},{id:`${d}_url`,val:`https://www.google.com/maps?q=place_id:${e.id}`},{id:`${d}_placename_short`,val:e.displayName}];for(let c in r){let a=document.getElementById(r[c].id);a.value=r[c].val,r[c].id=="google_places_field_latlong"&&p(`#${r[c].id}`).trigger("input")}return s=T(s),s}p(document).on("click","body.wp-autcomplete-results-open",()=>{p("#google-places-wp-ap-results").empty(),p("body").removeClass("wp-autcomplete-results-open")}),(async()=>{if(typeof google>"u")return;const{AutocompleteSuggestion:e}=await google.maps.importLibrary("places");let s={input:"",language:"en-US",region:"us"};s=T(s);let n=document.getElementById("google_places_field_placename");const l=document.createElement("div");l.id="google-places-wp-ap-results",n.parentElement.append(l),n.addEventListener("input",async r=>{if(r.target.value==""){l.replaceChildren(),p("body").removeClass("wp-autcomplete-results-open");return}s.input=r.target.value;const{suggestions:c}=await e.fetchAutocompleteSuggestions(s);let a=c.slice(0,5);l.children.length>=5&&l.replaceChildren(),p("body").addClass("wp-autcomplete-results-open");for(const t of a){const o=t.placePrediction,i=document.createElement("a");i.addEventListener("click",()=>{s=S(o.toPlace(),s,r)}),i.innerText=o.text.toString();const g=document.createElement("div");g.appendChild(i),l.appendChild(g)}})})();function O(e,s,n,l){const d=Math.pow(2,e.getZoom()),r=e.getProjection().fromLatLngToPoint(s),c=new google.maps.Point(n/d||0,l/d||0),a=new google.maps.Point(r.x+c.x,r.y-c.y),t=e.getProjection().fromPointToLatLng(a);e.panTo(t)}function I(e){let s=p(".google-places-wp-places-results-container"),n=s.find(`.google-places-wp-results tbody tr[data-pname=${e}]`);s.find("tr").removeClass("is-viewing"),n.addClass("is-viewing"),s.animate({scrollTop:n.offset().top-s.offset().top+s.scrollTop()},1e3)}function A(e){let s;for(let n=0;n<u.length;n++)if("id"in u[n]){if(u[n].id==e){s=n;break}}else if(u[n].location_id==e){s=n;break}return s}function b(){let e=p("#google_places_field_latlong"),s=e.val().split(","),n;if(!s.length){w("Error looking up places, invalid coordinates provided.");return}try{for(let l in s)if(isNaN(Number(s[l])))throw new Error;return n={lat:Number(s[0]),lng:Number(s[1])},n}catch{return w("Error looking up places, invalid coordinates provided."),!1}}p(document).ready(async e=>{if(typeof google>"u")return;const{Map:s,InfoWindow:n}=await google.maps.importLibrary("maps"),{Place:l}=await google.maps.importLibrary("places"),{LatLngBounds:d}=await google.maps.importLibrary("core");let r=e("#google_places_field_latlong"),c=b();if(x=n,c&&(h=new s(document.getElementById("admin-map"),{center:c,mapId:"DEMO_MAP_ID",zoom:15})),"saved_places"in gp_places){const a=new d;let t=!1;await N(gp_places.saved_places,l,a,!1).catch(o=>{t=!0,console.error(o)}),t||(h.fitBounds(a),e(".google-places-wp-save-results").prop("disabled",!1))}r.on("input",a=>{E("places"),h=new s(document.getElementById("admin-map"),{center:b(),mapId:"DEMO_MAP_ID",zoom:15})}),e("#google_places_field_ptype_selectall").on("click",a=>{e(a.target).is(":checked")?(e("[id^=google_places_field_ptype_]").prop("checked",!0),e(".controlled-radius-input").css({opacity:1}).prop("disabled",!1)):(e("[id^=google_places_field_ptype_]").prop("checked",!1),e(".controlled-radius-input").css({opacity:.8}).prop("disabled",!0))}),e(".control-parent").on("click",a=>{let t=e(a.target).next().next();e(a.target).is(":checked")?t.removeClass("parent-checked").css({opacity:1}).prop("disabled",!1):t.removeClass("parent-checked").css({opacity:.8}).prop("disabled",!0)}),e(document).on("click",".notice.is-dismissible",a=>{a.preventDefault(),e(void 0).closest(".notice").fadeOut()}),e("#google_places_settings").find("input").keypress(a=>{if(a.which==13)return e(void 0).next().focus(),!1}),e(".google-places-wp-find-places").on("click",a=>{a.preventDefault(),e(".google-places-wp-results tbody").empty(),M(l,d)}),e(document).on("click",".google-places-wp-results tbody tr td:first-child",a=>{let t=e(a.currentTarget).parent().data("pname"),o=A(t);I(t),google.maps.event.trigger(u[o].marker,"click"),O(h,u[o].marker.position,0,-180)}),e(".google-places-wp-save-results").on("click",a=>{a.preventDefault(),e(".loading_overlay").fadeIn();let t=[];for(let o=0;o<u.length;o++){let i={};for(let g in u[o])g!="marker"&&(i[g]=u[o][g]);t.push(i)}e.ajax({method:"POST",url:gp_places.adminurl,data:{prop_meta:t,action:"google_places_save_results"},success:o=>{if(o.success){let i=e(".google-places-wp-save-results"),g=i.text();i.addClass("results-saved-ok").text("Results saved ok!"),setTimeout(()=>{i.removeClass("results-saved-ok").text(g)},3e3)}},error:o=>{console.error(o),w("Error saving places, try restarting your search over and saving again.")}}),e(".loading_overlay").fadeOut()}),e(".google-places-wp-places-types form input").on("input",a=>{e(".google-places-wp-ptype-save-settings").prop("disabled",!1)}),e(".google-places-wp-places-types form").on("submit",a=>{a.preventDefault();let t=e(".google-places-wp-ptype-save-settings");t.prop("disabled",!0);let o=a.target,i=new FormData(o),g=o.querySelectorAll('input[type="checkbox"]'),y=[];i.append("action","google_places_save_places_settings");for(const[m,f]of i)y.push(m);for(let m of g)y.includes(m.name)||i.append(m.name,0);e.ajax({method:"POST",url:gp_places.adminurl,data:i,processData:!1,contentType:!1,enctype:"multipart/form-data",success:m=>{let f=t.text();m.success?t.addClass("success").text("Save successful!"):t.addClass("error").text("Error saving settings!"),setTimeout(()=>{t.removeClass("success").removeClass("error").text(f)},3500)}})}),e(document).on("click",".remove-place",a=>{let t=e(a.target).parent().parent().data("pname"),o=A(t);u[o].marker.setMap(null),e(`.google-places-wp-results table tr[data-pname="${t}"]`).remove(),u.splice(o,1)}),e(document).on("change",".google-places-wp-place-types-select",a=>{let t=e(a.currentTarget).parent().parent().data("pname"),o=e(`.google-places-wp-results table tr[data-pname="${t}"] .google-places-wp-place-types-select`).find(":selected").val();for(let i=0;i<u.length;i++)u[i].id==t&&(u[i].type=o.toLowerCase().replace(/ /g,"_"))}),e(".opener").on("click",()=>{let a=e(".google-places-wp-results-drawer"),t=a.hasClass("open"),o=e(".opener-chevron div");t?(a.removeClass("open"),o.removeClass("open")):(a.addClass("open"),o.addClass("open"))})}),p("#google-places-wp-tabs").fadeIn();async function M(e,s){const{SearchNearbyRankPreference:n}=await google.maps.importLibrary("places"),l=new s;let r=(()=>{let o=!1,i=500,g=[];return p("#google_places_field_radius_all").is(":checked")&&(o=!0,i=Number(p("#google_places_field_radius_all_radius").val())),p(".google-places-ptype-section:not(.google-places-wp-parent-group) input[type=checkbox]:not(#google_places_field_radius_all):not(#google_places_field_ptype_selectall)").each(function(y,m){let f={},k=m.name,C=m.name.replace("google_places_field_ptype_",""),_=this.checked?o?i:Number(p(`#${k}_radius`).val()):0;_&&(C=="entertainment"?["movie_theater","night_club"].map((P,R)=>{g.push({[P]:_})}):C=="shopping"?["shopping_mall"].map((P,R)=>{g.push({[P]:_})}):(f[C]=_,g.push(f)))}),g})(),c=!1,a=b();p("[id^=google_places_field_ptype_").parent().removeClass("place-no-results"),p(".loading-overlay").fadeIn(),h.setZoom(12);for(let o=0;o<u.length;o++)u[o].marker.setMap(null);let t={fields:["displayName","location","businessStatus"],locationRestriction:{center:a,radius:500},maxResultCount:5,rankPreference:n.POPULARITY,language:"en-US",region:"us"};for(let o=0;o<r.length;o++){let i=r[o];if(t.includedPrimaryTypes=Object.keys(i),t.locationRestriction.radius=i[t.includedPrimaryTypes[0]],await new Promise(async(y,m)=>{const{places:f}=await e.searchNearby(t);f.length?y(f):m("no_results")}).then(async y=>{await N(y,e,l).catch(m=>{c=!0,m.error_type=="fetchFields"&&w(`Error looking up place type ${Object.keys(i)[0].replace("_"," ")}. Try disabling this place type and search again.`)})}).catch(y=>{y=="no_results"&&p(`[name^=google_places_field_ptype_${Object.keys(i)[0]}]`).parent().addClass("place-no-results")}),c)break}!c&&u.length&&(h.fitBounds(l),p(".google-places-wp-save-results").prop("disabled",!1)),p(".loading-overlay").fadeOut()}function D(e){let s=["hospital","library","restaurant","pharmacy","school","shopping mall","train station","park","college","entertainment"],n=e.charAt(0).toUpperCase()+e.substr(1),l=[`<option value="${e}">${n}</option>`];return s.map((r,c)=>{let a=`<option value="${r}">${r}</option>`;l.push(a)}),`<select class="google-places-wp-place-types-select" name="google-places-wp-place-types-select">${l.join(" ")}</select>`}async function N(e,s,n,l=!0){const{AdvancedMarkerElement:d}=await google.maps.importLibrary("marker");return new Promise(async(r,c)=>{for(let a=0;a<e.length;a++){let t;if(l){let f;"id"in e[a]?f=e[a].id:f=e[a].location_id,t=new s({id:f});try{await t.fetchFields({fields:["displayName","formattedAddress","location","websiteURI","types","nationalPhoneNumber","photos"]})}catch(k){c({error_type:"fetchFields",msg:k})}if(!t.types)continue}else t={id:e[a].location_id,location:{lat:()=>parseFloat(e[a].lat),lng:()=>parseFloat(e[a].lng)},displayName:e[a].name,photos:e[a].photo.length?[{getURI:f=>e[a].photo}]:[],formattedAddress:e[a].pretty_address,phone:e[a].phone,types:[e[a].types]};let o=new x,i=new google.maps.LatLng(parseFloat(t.location.lat()),parseFloat(t.location.lng()));const g=new d({map:h,position:i,title:t.displayName});google.maps.event.addListener(g,"click",function(){v&&v.close();let f=`<div style="width: 200px;">${t.photos.length?`<span><img src="${t.photos[0].getURI({maxWidth:640})}" style="width: 100%;" /></span><br>`:""}<span>${t.displayName}</span><br><span>${t.formattedAddress}</span><br><a href="https://www.google.com/maps?q=place_id:${t.id}" target="_blank">View In Maps &raquo;</a></div>`;I(t.id),o.setContent(f),o.open(h,this),v=o}),n.extend(i);let y=`<tr data-pname="${t.id}"><td>${t.displayName}</td><td>${t.formattedAddress}</td><td><a href="https://www.google.com/maps?q=place_id:${t.id}" target="_blank">View In Maps &raquo;</a></td><td class="google-places-wp-place-type">${D(t.types[0].replace(/_/g," "))}</td><td class="remove-place-td"><span class="remove-place">X</span></td></tr>`,m;l?(m={name:t.displayName,id:t.id,address:t.formattedAddress,lat:t.location.lat(),lng:t.location.lng(),url:t.websiteURI,type:t.types[0].replace(/_/g," "),phone:t.nationalPhoneNumber,marker:g},t.photos.length&&(m.photo=t.photos[0].getURI({maxWidth:640}))):(e[a].marker=g,m=e[a]),u.push(m),p(".google-places-wp-results table.google-places-wp-results tbody").append(y)}r()})}};j(jQuery);
