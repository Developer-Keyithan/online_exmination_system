import { Toast } from './toast.js';

window.typeNIC = function (inputId) {
    let searchInput = $("#"+ inputId);

    searchInput.on("input", function (e) {
        let value = this.value;
        let cursorPosition = this.selectionStart;

        let raw = value.replace(/\s+/g, "");
        let newValue = "";

        if (/^\d/.test(raw)) {
            if (raw.length <= 10) {
                let first9 = raw.slice(0, 9);
                let tenthChar = raw.charAt(9) || "";

                if (/^\d{9}$/.test(first9)) {
                    if (/^[xXyYvV]$/.test(tenthChar)) {
                        raw = first9 + tenthChar.toUpperCase();
                    } else if (/^\d$/.test(tenthChar)) {
                        raw = first9 + tenthChar;
                    } else {
                        raw = first9;
                    }
                }
            } else {
                raw = raw.slice(0, 12).replace(/[^\d]/g, "");
            }

            if (raw.length > 0) {
                newValue = raw.slice(0, 4);
                if (raw.length > 4) newValue += " " + raw.slice(4, 8);
                if (raw.length > 8) newValue += " " + raw.slice(8);
            }
        } else {
            newValue = value;
        }

        this.value = newValue;

        let countSpacesBefore = (
            value.slice(0, cursorPosition).match(/\s/g) || []
        ).length;
        let countSpacesAfter = (
            newValue.slice(0, cursorPosition).match(/\s/g) || []
        ).length;
        let diff = countSpacesAfter - countSpacesBefore;
        this.selectionStart = this.selectionEnd = cursorPosition + diff;
    });
};