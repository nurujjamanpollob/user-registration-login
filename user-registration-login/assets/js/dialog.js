/**
 * Minimal Material Dialog.js - used to create dialog boxes, and show them on the screen
 * Developed by: Webbylife (https://webbylife.com)
 * Version: 1.0
 * @param {string} title - The title of the dialog box
 * @param {string} messageHTML - The message of the dialog box, supports HTML
 * @param {string} dialogContainerMaxWidth - The max width of the dialog box container, use this to set the max width of the dialog box
 * @param {array} buttons - An array of objects, each object should have a text property, and a function property (optional), when the button is clicked, the function will run
 * @returns {HTMLElement} - The dialog box
 */
function createAndShowDialog(title, messageHTML, dialogContainerMaxWidth, buttons) {
    // create a full screen dialog box div, and assign the top level div with the property name of --dialog
    let dialog = document.createElement('div');
    dialog.classList.add('minimal-material-dialog');
    // add attributes to the dialog box
    dialog.setAttribute('role', 'dialog');
    dialog.style.display = 'flex';
    dialog.style.justifyContent = 'center';
    dialog.style.alignItems = 'center';
    dialog.style.position = 'fixed';
    dialog.style.top = '0';
    dialog.style.left = '0';
    dialog.style.width = '100%';
    dialog.style.height = '100%';
    dialog.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
    dialog.style.zIndex = '1000';
    dialog.style.overflow = 'auto';
    dialog.style.padding = '20px';
    dialog.style.boxSizing = 'border-box';
    document.body.appendChild(dialog);

    // create a dialog box div, and assign the top level div with the property name of --dialog-box
    let dialogBox = document.createElement('div');
    dialogBox.classList.add('minimal-material-dialog-box');
    dialogBox.style.backgroundColor = 'white';
    dialogBox.style.width = '100%';

    // check if dialogContainerMaxWidth is set and not null or undefined or empty
    if (dialogContainerMaxWidth && dialogContainerMaxWidth !== '') {
        dialogBox.style.maxWidth = dialogContainerMaxWidth;
    } else {
        dialogBox.style.maxWidth = '500px'; // default max width
    }
    dialogBox.style.borderRadius = '5px';
    dialogBox.style.padding = '10px';
    dialogBox.style.boxSizing = 'border-box';
    dialog.appendChild(dialogBox);


    // create a dialog box title, and assign the top level div with the property name of --dialog-title
    let dialogTitle = document.createElement('div');
    dialogTitle.classList.add('minimal-material-dialog-title');
    dialogTitle.style.fontWeight = 'bold';
    dialogTitle.style.fontSize = '1.5em';
    dialogTitle.style.marginBottom = '10px';
    dialogTitle.innerText = title;
    dialogBox.appendChild(dialogTitle);

    // create a dialog box message, and assign the top level div with the property name of --dialog-message
    let dialogMessage = document.createElement('div');
    dialogMessage.classList.add('minimal-material-dialog-message');
    dialogMessage.innerHTML = messageHTML;
    dialogBox.appendChild(dialogMessage);

    // create a dialog box buttons, and assign the top level div with the property name of --dialog-buttons
    let dialogButtons = document.createElement('div');
    dialogButtons.classList.add('minimal-material-dialog-buttons');
    dialogButtons.style.display = 'flex';
    dialogButtons.style.justifyContent = 'flex-end';
    dialogButtons.style.marginTop = '10px';
    dialogBox.appendChild(dialogButtons);

    // loop through each button
    buttons.forEach((button) => {
        // create a dialog box button, and assign the top level div with the property name of --dialog-button
        let dialogButton = document.createElement('button');
        dialogButton.classList.add('minimal-material-dialog-dialog-button');
        dialogButton.style.marginLeft = '10px';
        dialogButton.textContent = button.text;
        // add event listener to each button
        dialogButton.addEventListener('click', () => {
            // check if the button provided onClick
            if (button.onClick) {
                // run the function
                button.onClick();
            }
        });
        dialogButtons.appendChild(dialogButton);
    });

    // return the dialog box
    return dialog;
}

// Close a dialog box
function closeDialog() {
    // find div with attribute role="dialog"
    let dialog = document.querySelector('[role="dialog"]');
    // check if dialog exists
    if (dialog) {
        // remove the dialog from the document
        dialog.remove();
    }

}

