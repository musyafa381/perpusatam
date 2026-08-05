/*
Template Name: Admin Template
Author: Wrappixel

File: js
*/
// ==============================================================
// Auto select left navbar (Fixed for subdirectories)
// ==============================================================
$(function () {
  "use strict";

  function highlightSidebar() {
    var currentPath = window.location.pathname;

    var sidebarLinks = $("ul#sidebarnav a.sidebar-link");
    sidebarLinks.removeClass("active");
    $("ul#sidebarnav li").removeClass("selected active");

    var bestMatch = null;
    var maxMatchLen = -1;

    sidebarLinks.each(function () {
      var href = $(this).attr("href");
      if (!href || href === "#" || href.startsWith("javascript:")) return;

      var linkPath = "";
      try {
        linkPath = new URL(this.href, window.location.origin).pathname;
      } catch (e) {
        linkPath = href;
      }

      if (currentPath === linkPath) {
        if (linkPath.length + 100 > maxMatchLen) {
          maxMatchLen = linkPath.length + 100;
          bestMatch = $(this);
        }
      } else if (currentPath.startsWith(linkPath.endsWith("/") ? linkPath : linkPath + "/")) {
        if (linkPath.length > maxMatchLen) {
          maxMatchLen = linkPath.length;
          bestMatch = $(this);
        }
      }
    });

    if (bestMatch) {
      bestMatch.addClass("active");
      bestMatch.closest("li.sidebar-item").addClass("selected");
    }
  }

  highlightSidebar();
  window.highlightSidebarMenu = highlightSidebar;

  $("#sidebarnav >li >a.has-arrow").on("click", function (e) {
    e.preventDefault();
  });
});