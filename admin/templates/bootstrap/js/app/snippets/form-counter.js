const updateCount = (counter, value) => {
	counter.get(0)[`step${value}`]()
}

$('[data-count-down]').on('click', (event) => {
	updateCount($(event.currentTarget).parents('.form-counter').find('input'), 'Down')
})

$('[data-count-up]').on('click', (event) => {
	updateCount($(event.currentTarget).parents('.form-counter').find('input'), 'Up')
})

$('.form-counter input[type=number]').on('keyup blur', (event) => {
	let $input = $(event.currentTarget)
	let min = parseInt($input.attr('min'))
	let max = parseInt($input.attr('max'))

	if($input.val() > max) $input.val(max)
	if($input.val() < min) $input.val(min)
})
