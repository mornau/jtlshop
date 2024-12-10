
export const debounce = (fn, wait = 100) => {
	let timeout
	return (...args) => {
		clearTimeout(timeout)
		timeout = setTimeout(() => fn(...args), wait)
	}
}