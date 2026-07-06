// Auto-adds a Show/Hide toggle button next to every password input on the page.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-secondary';
        btn.textContent = 'Show';
        btn.addEventListener('click', function () {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.textContent = showing ? 'Show' : 'Hide';
        });
        wrapper.appendChild(btn);
    });
});
