const $sidebar = $('#sidebar');

function setView(state = false)
{
	$sidebar[state ? 'addClass' : 'removeClass']('sidebar-collapsed');
	window.localStorage.setItem('jtlshop-sidebar-state', state.toString());
}

setView(window.localStorage.getItem('jtlshop-sidebar-state') === 'true');

$(document).on('click', '[data-toggle="sidebar-collapse"]', () => {
	setView(!$sidebar.hasClass('sidebar-collapsed'));
})
