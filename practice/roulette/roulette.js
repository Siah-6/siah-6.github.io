const WHEEL_NUMBERS = [
    0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26
];

const CHIPS = [10, 25, 50, 100, 250, 1250];

// Mapping of grid positions to numbers for logic
const GRID_NUMBERS = [
    [3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36], // Row 0 (Top)
    [2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35], // Row 1 (Mid)
    [1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34]  // Row 2 (Bot)
];

function getNumberColor(number) {
    if (number === 0) return "green";
    const redNumbers = [1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36];
    return redNumbers.includes(number) ? "red" : "black";
}

class RouletteGame {
    constructor() {
        this.balance = 10000;
        this.bets = {};
        this.betHistory = [];
        this.selectedChip = 10;
        this.isSpinning = false;
        this.rotation = 0;
        
        this.initWheel();
        this.initTable();
        this.initChips();
        this.updateUI();
    }

    initWheel() {
        const wheel = document.getElementById('wheel');
        wheel.innerHTML = ''; // Clear
        
        // Center Cap
        const cap = document.createElement('div');
        cap.className = "absolute inset-0 m-auto w-24 h-24 rounded-full bg-gradient-to-br from-yellow-600 to-yellow-800 shadow-xl border-4 border-yellow-500 flex items-center justify-center z-10";
        cap.innerHTML = `<div class="w-16 h-16 rounded-full bg-amber-950 border-2 border-yellow-500/50"></div>`;
        wheel.appendChild(cap);

        WHEEL_NUMBERS.forEach((num, index) => {
            const angle = (360 / WHEEL_NUMBERS.length) * index;
            const color = getNumberColor(num);
            const colorCode = color === 'green' ? '#059669' : color === 'red' ? '#dc2626' : '#171717';
            
            const wedge = document.createElement('div');
            wedge.className = 'absolute top-0 left-1/2 h-1/2 w-[24px] -ml-[12px] origin-bottom flex justify-center pt-1';
            wedge.style.transform = `rotate(${angle}deg)`;
            
            wedge.innerHTML = `
                <div class="h-full w-full absolute top-0 left-0 -z-10" 
                     style="background-color: ${colorCode}; clip-path: polygon(50% 100%, 0 0, 100% 0)">
                </div>
                <span class="text-white text-[11px] font-bold font-mono transform rotate-180 mt-2 drop-shadow-md wheel-number">${num}</span>
            `;
            wheel.appendChild(wedge);
        });
    }

    initTable() {
        const grid = document.getElementById('numbers-grid');
        grid.innerHTML = ''; // Clear

        // 1. Create Cells (Straight Bets)
        GRID_NUMBERS.forEach((rowNums, rowIndex) => {
            rowNums.forEach((num, colIndex) => {
                const color = getNumberColor(num);
                // Change: Background transparent, Text Color changes
                const textClass = color === 'red' ? 'num-red' : 'num-black';
                
                const cell = document.createElement('div');
                cell.className = `table-cell h-12 md:h-16 font-serif text-lg md:text-xl font-bold ${textClass}`;
                cell.style.gridRow = rowIndex + 1;
                cell.style.gridColumn = colIndex + 1;
                // Important: Stop propagation so clicking cell doesn't trigger underlying things, 
                // though z-index handles zones above.
                cell.onclick = (e) => { 
                    // e.stopPropagation(); 
                    this.placeBet('straight', num); 
                }; 
                
                cell.innerHTML = `
                    <span class="rotate-90 md:rotate-0">${num}</span>
                    <div id="chip-straight-${num}" class="chip-marker absolute inset-0 m-auto flex items-center justify-center"></div>
                `;
                grid.appendChild(cell);
            });
        });

        // 2. Create Zones
        
        // Vertical Splits (Between rows)
        for (let r = 0; r < 2; r++) { 
            for (let c = 0; c < 12; c++) {
                const numTop = GRID_NUMBERS[r][c];
                const numBot = GRID_NUMBERS[r+1][c];
                const zone = this.createZone('zone-split-v', `split-${numTop},${numBot}`);
                zone.style.gridRow = r + 2; 
                zone.style.gridColumn = c + 1;
                zone.onclick = (e) => { e.stopPropagation(); this.placeBet('split', `${numTop},${numBot}`); };
                
                zone.innerHTML = `<div id="chip-split-${numTop},${numBot}" class="chip-marker w-full h-full flex items-center justify-center transform scale-75"></div>`;
                grid.appendChild(zone);
            }
        }

        // Horizontal Splits (Between columns)
        for (let r = 0; r < 3; r++) {
            for (let c = 0; c < 11; c++) { 
                const numLeft = GRID_NUMBERS[r][c];
                const numRight = GRID_NUMBERS[r][c+1];
                const zone = this.createZone('zone-split-h', `split-${numLeft},${numRight}`);
                zone.style.gridRow = r + 1;
                zone.style.gridColumn = c + 1; 
                zone.onclick = (e) => { e.stopPropagation(); this.placeBet('split', `${numLeft},${numRight}`); };
                
                zone.innerHTML = `<div id="chip-split-${numLeft},${numRight}" class="chip-marker w-full h-full flex items-center justify-center transform scale-75"></div>`;
                grid.appendChild(zone);
            }
        }

        // Corners (Intersection of 4 numbers)
        for (let r = 0; r < 2; r++) {
            for (let c = 0; c < 11; c++) {
                const n1 = GRID_NUMBERS[r][c];     // Top Left
                const n2 = GRID_NUMBERS[r][c+1];   // Top Right
                const n3 = GRID_NUMBERS[r+1][c];   // Bot Left
                const n4 = GRID_NUMBERS[r+1][c+1]; // Bot Right
                // Note: Numbers logic doesn't matter for ID uniqueness as long as consistent
                const zone = this.createZone('zone-corner', `corner-${n1},${n2},${n3},${n4}`);
                zone.style.gridRow = r + 2; 
                zone.style.gridColumn = c + 1; 
                zone.onclick = (e) => { e.stopPropagation(); this.placeBet('corner', `${n1},${n2},${n3},${n4}`); };
                
                zone.innerHTML = `<div id="chip-corner-${n1},${n2},${n3},${n4}" class="chip-marker w-full h-full flex items-center justify-center transform scale-50"></div>`;
                grid.appendChild(zone);
            }
        }

        // Street Bets (Top of column - 3 numbers)
        // In this layout, Row 0 is top (3, 6, 9).
        for (let c = 0; c < 12; c++) {
            const n1 = GRID_NUMBERS[2][c]; // 1 (Bot)
            const n2 = GRID_NUMBERS[1][c]; // 2 (Mid)
            const n3 = GRID_NUMBERS[0][c]; // 3 (Top)
            const zone = this.createZone('zone-street', `street-${n1},${n2},${n3}`);
            zone.style.gridRow = 1; 
            zone.style.gridColumn = c + 1;
            zone.onclick = (e) => { e.stopPropagation(); this.placeBet('street', `${n1},${n2},${n3}`); };
            
            zone.innerHTML = `<div id="chip-street-${n1},${n2},${n3}" class="chip-marker w-full h-full flex items-center justify-center transform scale-75"></div>`;
            grid.appendChild(zone);
        }

        // Six Line Bets (Intersection of streets)
        for (let c = 0; c < 11; c++) {
            const col1 = [GRID_NUMBERS[2][c], GRID_NUMBERS[1][c], GRID_NUMBERS[0][c]];
            const col2 = [GRID_NUMBERS[2][c+1], GRID_NUMBERS[1][c+1], GRID_NUMBERS[0][c+1]];
            const nums = [...col1, ...col2].join(',');
            
            const zone = this.createZone('zone-sixline', `sixline-${nums}`);
            zone.style.gridRow = 1;
            zone.style.gridColumn = c + 1;
            zone.onclick = (e) => { e.stopPropagation(); this.placeBet('sixline', nums); };
            
            zone.innerHTML = `<div id="chip-sixline-${nums}" class="chip-marker w-full h-full flex items-center justify-center transform scale-50"></div>`;
            grid.appendChild(zone);
        }
    }

    createZone(className, id) {
        const div = document.createElement('div');
        div.className = `hit-zone ${className}`;
        // div.title = id; // Helper tooltip
        return div;
    }

    initChips() {
        const container = document.getElementById('chip-container');
        CHIPS.forEach(val => {
            const btn = document.createElement('button');
            btn.onclick = () => this.selectChip(val);
            btn.className = `relative w-12 h-12 rounded-full flex items-center justify-center font-bold text-[10px] shadow-lg transition-transform active:scale-95 chip-shadow border-2 border-dashed text-white chip-btn ${this.getChipColor(val)}`;
            if (val === this.selectedChip) btn.classList.add('ring-2', 'ring-casino-gold', 'ring-offset-2', 'ring-offset-black', '-translate-y-2');
            btn.innerHTML = `<div class="absolute inset-1 rounded-full border border-white/30"></div>${val}`;
            btn.dataset.val = val;
            container.appendChild(btn);
        });
    }

    getChipColor(val) {
        if (val === 10) return "bg-blue-600 border-white";
        if (val === 25) return "bg-orange-600 border-white";
        if (val === 50) return "bg-zinc-800 border-white";
        if (val === 100) return "bg-purple-600 border-white";
        if (val === 250) return "bg-red-600 border-white";
        return "bg-green-600 border-white";
    }

    selectChip(val) {
        this.selectedChip = val;
        document.querySelectorAll('.chip-btn').forEach(btn => {
            btn.classList.remove('ring-2', 'ring-casino-gold', 'ring-offset-2', 'ring-offset-black', '-translate-y-2');
            if (parseInt(btn.dataset.val) === val) {
                btn.classList.add('ring-2', 'ring-casino-gold', 'ring-offset-2', 'ring-offset-black', '-translate-y-2');
            }
        });
    }

    placeBet(type, value) {
        if (this.isSpinning) return;
        if (this.balance < this.selectedChip) {
            alert("Insufficient funds");
            return;
        }

        this.betHistory.push(JSON.parse(JSON.stringify(this.bets)));
        const key = `${type}-${value}`;
        this.bets[key] = (this.bets[key] || 0) + this.selectedChip;
        this.balance -= this.selectedChip;
        this.updateUI();
    }

    undo() {
        if (this.isSpinning || this.betHistory.length === 0) return;
        const lastBets = this.betHistory.pop();
        const currentTotal = Object.values(this.bets).reduce((a, b) => a + b, 0);
        const lastTotal = Object.values(lastBets).reduce((a, b) => a + b, 0);
        this.balance += (currentTotal - lastTotal);
        this.bets = lastBets;
        this.updateUI();
    }

    clearBets() {
        if (this.isSpinning || Object.keys(this.bets).length === 0) return;
        const totalBets = Object.values(this.bets).reduce((a, b) => a + b, 0);
        this.balance += totalBets;
        this.bets = {};
        this.betHistory = [];
        this.updateUI();
    }

    spin() {
        if (this.isSpinning) return;
        const totalBet = Object.values(this.bets).reduce((a, b) => a + b, 0);
        if (totalBet === 0) return;

        this.isSpinning = true;
        this.updateUI();
        document.getElementById('result-overlay').classList.add('hidden');

        const prizeNumber = Math.floor(Math.random() * 37);
        const newPrizeIndex = WHEEL_NUMBERS.indexOf(prizeNumber);
        const singleWedgeAngle = 360 / WHEEL_NUMBERS.length;
        
        // Calculate rotation
        const currentRotation = this.rotation;
        const targetRotation = currentRotation + 1440 + (360 - (newPrizeIndex * singleWedgeAngle)) - (currentRotation % 360);
        
        this.rotation = targetRotation;

        const wheel = document.getElementById('wheel');
        wheel.style.transform = `rotate(${targetRotation}deg)`;

        setTimeout(() => {
            this.handleSpinEnd(prizeNumber);
        }, 4000);
    }

    handleSpinEnd(prizeNumber) {
        this.isSpinning = false;
        
        const overlay = document.getElementById('result-overlay');
        const resultNum = document.getElementById('result-number');
        resultNum.innerText = prizeNumber;
        
        const color = getNumberColor(prizeNumber);
        resultNum.className = `text-6xl font-serif drop-shadow-[0_0_10px_rgba(255,215,0,0.8)] animate-in zoom-in fade-in duration-500 ${color === 'red' ? 'text-red-500' : color === 'green' ? 'text-green-500' : 'text-white'}`;
        
        overlay.classList.remove('hidden');

        const historyContainer = document.getElementById('history-container');
        const bgClass = color === 'red' ? 'bg-casino-red' : color === 'black' ? 'bg-black border-white/30' : 'bg-green-600';
        const badge = document.createElement('div');
        badge.className = `w-8 h-8 rounded flex-shrink-0 flex items-center justify-center text-xs font-bold border border-white/10 ${bgClass} animate-in slide-in-from-right`;
        badge.innerText = prizeNumber;
        historyContainer.prepend(badge);
        if (historyContainer.children.length > 10) historyContainer.lastChild.remove();

        // Calculate Winnings
        let winnings = 0;
        Object.entries(this.bets).forEach(([key, amount]) => {
            const [type, value] = key.split('-');
            let won = false;
            let payout = 0;

            if (type === 'straight' && parseInt(value) === prizeNumber) { 
                won = true; payout = 30; 
            }
            else if (type === 'split') {
                const nums = value.split(',').map(Number);
                if (nums.includes(prizeNumber)) { won = true; payout = 17; }
            }
            else if (type === 'street') {
                const nums = value.split(',').map(Number);
                if (nums.includes(prizeNumber)) { won = true; payout = 11; }
            }
            else if (type === 'corner') {
                const nums = value.split(',').map(Number);
                if (nums.includes(prizeNumber)) { won = true; payout = 8; }
            }
            else if (type === 'sixline') {
                const nums = value.split(',').map(Number);
                if (nums.includes(prizeNumber)) { won = true; payout = 5; }
            }
            else if (type === 'red' && getNumberColor(prizeNumber) === 'red') { won = true; payout = 1; }
            else if (type === 'black' && getNumberColor(prizeNumber) === 'black') { won = true; payout = 1; }
            else if (type === 'even' && prizeNumber !== 0 && prizeNumber % 2 === 0) { won = true; payout = 1; }
            else if (type === 'odd' && prizeNumber !== 0 && prizeNumber % 2 !== 0) { won = true; payout = 1; }
            else if (type === 'low' && prizeNumber >= 1 && prizeNumber <= 18) { won = true; payout = 1; }
            else if (type === 'high' && prizeNumber >= 19 && prizeNumber <= 36) { won = true; payout = 1; }
            else if (type === 'dozen') {
                const d = parseInt(value);
                if ((d === 1 && prizeNumber <= 12 && prizeNumber >= 1) ||
                    (d === 2 && prizeNumber >= 13 && prizeNumber <= 24) ||
                    (d === 3 && prizeNumber >= 25)) { won = true; payout = 2; }
            }
            else if (type === 'column') {
                const c = parseInt(value);
                if (prizeNumber !== 0) {
                    if (c === 1 && prizeNumber % 3 === 1) { won = true; payout = 2; }
                    if (c === 2 && prizeNumber % 3 === 2) { won = true; payout = 2; }
                    if (c === 3 && prizeNumber % 3 === 0) { won = true; payout = 2; }
                }
            }

            if (won) winnings += amount * (payout + 1);
        });

        if (winnings > 0) {
            this.balance += winnings;
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#d4af37', '#ffffff', '#8a0000']
            });
            setTimeout(() => {
                const msg = document.createElement('div');
                msg.className = "fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black/90 text-casino-gold border-2 border-casino-gold px-8 py-4 rounded-xl z-50 text-2xl font-bold shadow-2xl animate-in zoom-in fade-in";
                msg.innerText = `YOU WON $${winnings.toLocaleString()}!`;
                document.body.appendChild(msg);
                setTimeout(() => msg.remove(), 2000);
            }, 500);
        }

        this.betHistory = []; 
        this.updateUI();
    }

    updateUI() {
        document.getElementById('balance-display').innerText = `$${this.balance.toLocaleString()}`;
        const totalBet = Object.values(this.bets).reduce((a, b) => a + b, 0);
        document.getElementById('bet-display').innerText = `$${totalBet.toLocaleString()}`;
        
        const spinBtn = document.getElementById('spin-btn');
        spinBtn.disabled = this.isSpinning || totalBet === 0;
        spinBtn.innerText = this.isSpinning ? 'Spinning...' : 'Spin';

        // Render chips
        document.querySelectorAll('.chip-marker').forEach(el => {
            el.innerHTML = '';
        });

        Object.entries(this.bets).forEach(([key, amount]) => {
            const [type, ...valParts] = key.split('-');
            const value = valParts.join('-');
            
            let elId = `chip-${type}-${value}`;
            const el = document.getElementById(elId);
            
            if (el) {
                el.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-white border-2 border-dashed border-red-500 shadow-lg flex items-center justify-center z-50 pointer-events-none animate-in zoom-in duration-200 select-none">
                        <span class="text-[9px] font-bold text-black leading-none">${this.formatChipAmount(amount)}</span>
                    </div>
                `;
            }
        });
    }

    formatChipAmount(amount) {
        if (amount >= 1000) return (amount/1000) + 'k';
        return amount;
    }
}

const game = new RouletteGame();


