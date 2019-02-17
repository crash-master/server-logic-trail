window.onload = function(){
	let showBtn = document.querySelector('.sysinfo-container .show-info');
	showBtn.addEventListener('click', function(){
		let container = document.querySelector('.sysinfo-container');
		container.classList.toggle('show');

	});
}