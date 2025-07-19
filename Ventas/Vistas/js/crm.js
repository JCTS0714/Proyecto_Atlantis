document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const columns = {
        'nuevo': document.getElementById('nuevo-column'),
        'calificado': document.getElementById('calificado-column'),
        'propuesto': document.getElementById('propuesto-column'),
        'ganado': document.getElementById('ganado-column')
    };
    
    let draggedCard = null;
    
    // Funcionalidad para añadir tarjetas
    const addCardBtn = document.getElementById('add-card-btn');
    const newCardInput = document.getElementById('new-card-text');
    
    addCardBtn.addEventListener('click', function() {
        const cardText = newCardInput.value.trim();
        if (cardText) {
            addCard('nuevo', cardText);
            newCardInput.value = '';
        }
    });
    
    newCardInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const cardText = newCardInput.value.trim();
            if (cardText) {
                addCard('nuevo', cardText);
                newCardInput.value = '';
            }
        }
    });
    
    // Funcionalidad para mover tarjetas con botones
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('move-btn')) {
            const targetColumn = e.target.getAttribute('data-target');
            const card = e.target.closest('.kanban-card');
            moveCard(card, targetColumn);
        }
        
        if (e.target.classList.contains('text-gray-400')) {
            e.target.closest('.kanban-card').remove();
        }
    });
    
    // Drag and Drop
    document.querySelectorAll('.kanban-card').forEach(card => {
        setupCardDragEvents(card);
    });
    
    // Funciones auxiliares
    function addCard(columnId, title) {
        const column = columns[columnId];
        const cardId = 'card-' + Date.now();
        
        const buttons = {
            'nuevo': '<button class="move-btn bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200" data-target="calificado">→</button>',
            'calificado': '<button class="move-btn bg-green-100 text-green-600 px-2 py-1 rounded hover:bg-green-200" data-target="propuesto">→</button>',
            'propuesto': '<button class="move-btn bg-yellow-100 text-yellow-600 px-2 py-1 rounded hover:bg-yellow-200" data-target="ganado">→</button>',
            'ganado': '<button class="move-btn bg-purple-100 text-purple-600 px-2 py-1 rounded hover:bg-purple-200" disabled>✓</button>'
        };
        
        const colors = {
            'nuevo': 'blue',
            'calificado': 'green',
            'propuesto': 'yellow',
            'ganado': 'purple'
        };
        
        const card = document.createElement('div');
        card.className = `kanban-card bg-white rounded-lg shadow p-4 cursor-move border-l-4 border-${colors[columnId]}-500`;
        card.setAttribute('draggable', 'true');
        card.dataset.id = cardId;
        
        card.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-medium text-gray-800">${title}</h3>
                <button class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <p class="text-gray-600 text-sm mb-3">Detalles del contacto...</p>
            <div class="flex justify-between text-xs text-gray-500">
                <span>Creado: Hoy</span>
                <div class="space-x-2">
                    ${buttons[columnId]}
                </div>
            </div>
        `;
        
        setupCardDragEvents(card);
        column.prepend(card);
    }
    
    function moveCard(card, targetColumnId) {
        const targetColumn = columns[targetColumnId];
        
        // Actualizar botones según la nueva columna
        const cardButtons = card.querySelector('.space-x-2');
        
        if (targetColumnId === 'calificado') {
            cardButtons.innerHTML = '<button class="move-btn bg-green-100 text-green-600 px-2 py-1 rounded hover:bg-green-200" data-target="propuesto">→</button>';
            card.classList.remove('border-blue-500', 'border-yellow-500', 'border-purple-500');
            card.classList.add('border-green-500');
        } else if (targetColumnId === 'propuesto') {
            cardButtons.innerHTML = '<button class="move-btn bg-yellow-100 text-yellow-600 px-2 py-1 rounded hover:bg-yellow-200" data-target="ganado">→</button>';
            card.classList.remove('border-blue-500', 'border-green-500', 'border-purple-500');
            card.classList.add('border-yellow-500');
        } else if (targetColumnId === 'ganado') {
            cardButtons.innerHTML = '<button class="move-btn bg-purple-100 text-purple-600 px-2 py-1 rounded hover:bg-purple-200" disabled>✓</button>';
            card.classList.remove('border-blue-500', 'border-green-500', 'border-yellow-500');
            card.classList.add('border-purple-500');
        }
        
        targetColumn.prepend(card);
    }
    
    function setupCardDragEvents(card) {
        card.addEventListener('dragstart', function(e) {
            draggedCard = this;
            setTimeout(() => {
                this.classList.add('kanban-card-moving');
            }, 0);
        });
        
        card.addEventListener('dragend', function() {
            this.classList.remove('kanban-card-moving');
        });
    }
    
    // Configurar eventos para las columnas
    Object.values(columns).forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.backgroundColor = 'rgba(0, 0, 0, 0.05)';
        });
        
        column.addEventListener('dragleave', function() {
            this.style.backgroundColor = '';
        });
        
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '';
            
            if (draggedCard) {
                this.prepend(draggedCard);
            }
        });
    });
});
