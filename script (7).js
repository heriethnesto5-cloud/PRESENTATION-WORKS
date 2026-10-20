const display = document.getElementById('display');
const message = document.getElementById('message');
const buttons = document.querySelectorAll('.key');

let currentExpression = '';

function updateDisplay(value) {
    display.value = value;
}

function setMessage(text) {
    message.textContent = text;
}

function isOperator(char) {
    return ['+', '-', '*', '/'].includes(char);
}

function appendValue(value) {
    if (value === '.' && currentExpression.includes('.')) {
        const lastNumber = currentExpression.split(/[+\-*/]/).at(-1);
        if (lastNumber.includes('.')) {
            return;
        }
    }

    if (value === '.' && currentExpression === '') {
        currentExpression = '0.';
        updateDisplay(currentExpression);
        return;
    }

    if (isOperator(value)) {
        if (currentExpression === '') {
            return;
        }

        const lastChar = currentExpression[currentExpression.length - 1];
        if (isOperator(lastChar)) {
            currentExpression = currentExpression.slice(0, -1) + value;
            updateDisplay(currentExpression);
            return;
        }
    }

    currentExpression += value;
    updateDisplay(currentExpression);
    setMessage('');
}

function clearCalculator() {
    currentExpression = '';
    updateDisplay('0');
    setMessage('');
}

function calculateExpression() {
    if (!currentExpression) {
        setMessage('Enter a calculation first.');
        return;
    }

    const lastChar = currentExpression[currentExpression.length - 1];
    if (isOperator(lastChar)) {
        setMessage('Calculation is incomplete.');
        return;
    }

    const sanitizedExpression = currentExpression
        .replace(/×/g, '*')
        .replace(/÷/g, '/');

    const validPattern = /^[0-9+\-*/.()\s]+$/;
    if (!validPattern.test(sanitizedExpression)) {
        setMessage('Invalid calculation.');
        return;
    }

    try {
        const result = Function(`"use strict"; return (${sanitizedExpression});`)();

        if (!Number.isFinite(result)) {
            setMessage('Cannot divide by zero.');
            return;
        }

        currentExpression = String(result);
        updateDisplay(currentExpression);
        setMessage(`Answer: ${result}`);
    } catch (error) {
        setMessage('Invalid calculation.');
    }
}

buttons.forEach((button) => {
    button.addEventListener('click', () => {
        const action = button.dataset.action;
        const value = button.dataset.value;

        if (action === 'clear') {
            clearCalculator();
            return;
        }

        if (action === 'calculate') {
            calculateExpression();
            return;
        }

        appendValue(value);
    });
});

updateDisplay('0');
