/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(".thumb").on("error", function () {
    $(this).css({visibility: "hidden"});
}).attr("src", "cambiado.jpg");
$("img").on("error", function () {
    $(this).css({visibility: "hidden"});
}).attr("src", "cambiado.jpg");
$("img.thumb").on("error", function () {
    $(this).css({visibility: "hidden"});
}).attr("src", "cambiado.jpg");
$("#albumTable").on("error.dt", function () {
    $(this).css({visibility: "hidden"});
}).attr("src", "cambiado.jpg");
$("img.thumb").on("error.dt", function () {
    $(this).css({visibility: "hidden"});
}).attr("src", "cambiado.jpg");
