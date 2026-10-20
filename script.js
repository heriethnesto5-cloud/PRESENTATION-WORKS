// Calculator state and display elements.
const expressionDisplay = document.querySelector('#expression-display');
const resultDisplay = document.querySelector('#result-display');
const keypad = document.querySelector('#keypad');
const historyList = document.querySelector('#history-list');
const emptyHistory = document.querySelector('#empty-history');
const themeToggle = document.querySelector('#theme-toggle');
const clearHistoryButton = document.querySelector('#clear-history');
const modeButtons = document.querySelectorAll('.mode-button');

let expression = '';
let lastResult = '';
let history = JSON.parse(localStorage.getItem('calcura-history') || '[]');

const operators = new Set(['+', '-', '×', '÷', '%', '^']);
const functions = new Set(['sin', 'cos', 'tan', 'log', 'ln', 'sqrt']);

function updateDisplay() {
    expressionDisplay.textContent = expression || '0';
    resultDisplay.textContent = lastResult || '0';
}

function appendValue(value) {
    if (lastResult && !operators.has(value) && value !== ')' && value !== '%') {
        expression = '';
    }
    lastResult = '';
    expression += value;
    updateDisplay();
}

function clearCalculator() {
    expression = '';
    lastResult = '';
    updateDisplay();
}

function deleteLast() {
    expression = expression.slice(0, -1);
    lastResult = '';
    updateDisplay();
}

function formatNumber(value) {
    if (!Number.isFinite(value)) throw new Error('Result is not finite');
    return Number(value.toPrecision(12)).toString();
}

function factorial(value) {
    if (!Number.isInteger(value) || value < 0 || value > 170) {
        throw new Error('Factorial needs a whole number from 0 to 170');
    }
    let answer = 1;
    for (let index = 2; index <= value; index += 1) answer *= index;
    return answer;
}

// Tokenize the expression so operators and scientific functions are handled safely.
function tokenize(input) {
    const tokens = [];
    let index = 0;
    while (index < input.length) {
        const character = input[index];
        if (/\s/.test(character)) { index += 1; continue; }
        if (/\d|\./.test(character)) {
            const match = input.slice(index).match(/^(?:\d+\.?\d*|\.\d+)/);
            tokens.push({ type: 'number', value: Number(match[0]) });
            index += match[0].length;
            continue;
        }
        if (/[a-z]/i.test(character)) {
            const match = input.slice(index).match(/^[a-z]+/i)[0].toLowerCase();
            if (!functions.has(match)) throw new Error(`Unknown function: ${match}`);
            tokens.push({ type: 'function', value: match });
            index += match.length;
            continue;
        }
        if (character === 'π') { tokens.push({ type: 'number', value: Math.PI }); index += 1; continue; }
        if (character === 'e') { tokens.push({ type: 'number', value: Math.E }); index += 1; continue; }
        if ('()+-×÷%^!'.includes(character)) {
            tokens.push({ type: character === '(' || character === ')' ? 'parenthesis' : 'operator', value: character });
            index += 1;
            continue;
        }
        if (character === '√') { tokens.push({ type: 'function', value: 'sqrt' }); index += 1; continue; }
        throw new Error(`Unexpected character: ${character}`);
    }
    return tokens;
}

// Recursive descent parsing gives parentheses and powers their normal precedence.
function evaluate(input) {
    const tokens = tokenize(input);
    let position = 0;
    const peek = () => tokens[position];
    const consume = (value) => {
        if (peek()?.value !== value) throw new Error(`Expected ${value}`);
        position += 1;
    };

    function parsePrimary() {
        const token = peek();
        if (!token) throw new Error('Incomplete expression');
        if (token.type === 'number') { position += 1; return token.value; }
        if (token.value === '(') {
            position += 1;
            const value = parseAdditive();
            consume(')');
            return value;
        }
        if (token.value === '+' || token.value === '-') {
            position += 1;
            const value = parsePrimary();
            return token.value === '-' ? -value : value;
        }
        if (token.type === 'function') {
            position += 1;
            consume('(');
            const value = parseAdditive();
            consume(')');
            const angle = value * Math.PI / 180;
            if (token.value === 'sin') return Math.sin(angle);
            if (token.value === 'cos') return Math.cos(angle);
            if (token.value === 'tan') return Math.tan(angle);
            if (token.value === 'log') { if (value <= 0) throw new Error('Log needs a positive number'); return Math.log10(value); }
            if (token.value === 'ln') { if (value <= 0) throw new Error('Ln needs a positive number'); return Math.log(value); }
            if (value < 0) throw new Error('Square root needs a positive number');
            return Math.sqrt(value);
        }
        throw new Error('Invalid expression');
    }

    function parsePower() {
        let value = parsePrimary();
        if (peek()?.value === '^') { position += 1; value = value ** parsePower(); }
        if (peek()?.value === '!') { position += 1; value = factorial(value); }
        return value;
    }

    function parseMultiplicative() {
        let value = parsePower();
        while (['×', '÷', '%'].includes(peek()?.value)) {
            const operator = tokens[position++].value;
            const next = parsePower();
            if (operator === '×') value *= next;
            if (operator === '÷') { if (next === 0) throw new Error('Cannot divide by zero'); value /= next; }
            if (operator === '%') value %= next;
        }
        return value;
    }

    function parseAdditive() {
        let value = parseMultiplicative();
        while (['+', '-'].includes(peek()?.value)) {
            const operator = tokens[position++].value;
            const next = parseMultiplicative();
            value = operator === '+' ? value + next : value - next;
        }
        return value;
    }

    const result = parseAdditive();
    if (position < tokens.length) throw new Error('Check your parentheses and operators');
    return result;
}

function calculate() {
    if (!expression) return;
    try {
        const result = formatNumber(evaluate(expression));
        lastResult = result;
        history.unshift({ expression, result });
        history = history.slice(0, 12);
        localStorage.setItem('calcura-history', JSON.stringify(history));
        renderHistory();
        updateDisplay();
    } catch (error) {
        lastResult = error.message;
        resultDisplay.classList.add('error');
        window.setTimeout(() => resultDisplay.classList.remove('error'), 700);
        updateDisplay();
    }
}

function transformLastNumber(transform) {
    if (!expression) return;
    const match = expression.match(/(\d+(?:\.\d+)?)$/);
    if (!match) { lastResult = 'Select a number first'; updateDisplay(); return; }
    const value = Number(match[1]);
    try {
        expression = expression.slice(0, match.index) + transform(value);
        lastResult = '';
        updateDisplay();
    } catch (error) { lastResult = error.message; updateDisplay(); }
}

function renderHistory() {
    historyList.querySelectorAll('.history-item').forEach((item) => item.remove());
    emptyHistory.style.display = history.length ? 'none' : 'block';
    history.forEach((item) => {
        const element = document.createElement('div');
        element.className = 'history-item';
        element.innerHTML = `<div class="history-expression">${item.expression} =</div><div class="history-result">${item.result}</div>`;
        element.addEventListener('click', () => { expression = item.expression; lastResult = item.result; updateDisplay(); });
        historyList.appendChild(element);
    });
}

keypad.addEventListener('click', (event) => {
    const button = event.target.closest('button');
    if (!button) return;
    const { action, value } = button.dataset;
    if (value) appendValue(value);
    if (action === 'clear') clearCalculator();
    if (action === 'delete') deleteLast();
    if (action === 'equals') calculate();
    if (action === 'square') transformLastNumber((number) => `${number}^2`);
    if (action === 'factorial') transformLastNumber(factorial);
    if (action === 'reciprocal') transformLastNumber((number) => { if (number === 0) throw new Error('Cannot divide by zero'); return `1/${number}`; });
    if (action === 'toggle-sign') transformLastNumber((number) => `${-number}`);
});

modeButtons.forEach((button) => button.addEventListener('click', () => {
    modeButtons.forEach((item) => item.classList.toggle('active', item === button));
    document.body.classList.toggle('scientific-mode', button.dataset.mode === 'scientific');
}));

document.addEventListener('keydown', (event) => {
    const keyMap = { '*': '×', '/': '÷' };
    if (/^[0-9.]$/.test(event.key) || '+-()%'.includes(event.key) || keyMap[event.key]) {
        event.preventDefault();
        appendValue(keyMap[event.key] || event.key);
    } else if (event.key === 'Enter' || event.key === '=') { event.preventDefault(); calculate();
    } else if (event.key === 'Backspace') deleteLast();
    else if (event.key === 'Escape') clearCalculator();
});

themeToggle.addEventListener('click', () => {
    const isDark = document.body.dataset.theme === 'dark';
    document.body.dataset.theme = isDark ? 'light' : 'dark';
    themeToggle.setAttribute('aria-label', isDark ? 'Switch to dark mode' : 'Switch to light mode');
});

clearHistoryButton.addEventListener('click', () => {
    history = [];
    localStorage.removeItem('calcura-history');
    renderHistory();
});

renderHistory();
updateDisplay();
