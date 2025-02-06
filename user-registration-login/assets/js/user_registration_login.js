// when page loads, listen
document.addEventListener('DOMContentLoaded', () => {
    // get html elements with attribute materialize="true"
    const materializeElements = document.querySelectorAll('[materialize="true"]');

    // loop through each element
    materializeElements.forEach((element) => {
        // add event listener to each element
        element.addEventListener('input', () => {
            // set value attribute to the value of the input, if the value is not empty
            if (element.value !== '') {
                element.setAttribute('value', element.value);
            }
        });
    });
});