/* [ZASO] Alert Box Template - Main JS */

document.addEventListener("DOMContentLoaded", function () {
	var closeBtn = document.querySelector(".zaso-alert-box__closebtn");
	closeBtn.addEventListener("click", function (event) {
		event.preventDefault();
		this.closest(".zaso-alert-box").style.display = "none";
	});
});
