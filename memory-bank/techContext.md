# Technical Context

## Frontend Architecture

### JavaScript Modules

- Modular design with ES6 modules
- Each module has a specific responsibility
- Clear dependency management
- Event-driven architecture

### Module Structure

```
public/js/modules/
├── AuthManager.js      # Authentication handling
├── DateFormatter.js    # Date formatting utilities
├── ModalManager.js     # Modal dialog management
├── PastSessionsManager.js # Past sessions handling
├── RunManager.js       # Run entry management
├── SessionManager.js   # Session state management
├── SettingsManager.js  # User settings management
├── StatsManager.js     # Statistics calculations
└── UIManager.js        # UI updates and rendering
```

### CSS Organization

- Mobile-first responsive design
- Semantic class naming
- Dark mode support via data-attributes
- Responsive breakpoints:

  ```css
  /* Mobile styles */
  @media (max-width: 991.98px) {
    /* Tablet and smaller */
  }

  /* Small mobile styles */
  @media (max-width: 375px) {
    /* Small phones */
  }
  ```

## Data Management

### Session Management

- Active session tracking
- Session state persistence
- Session switching
- Session completion
- Past sessions view

### Statistics Calculation

- Daily average based on days since start
- Estimated total based on current average
- Target probability calculation:
  ```javascript
  // Base probability on estimated progress, weighted by current progress
  const currentWeight = 0.3; // 30% weight
  const estimatedWeight = 0.7; // 70% weight
  probability =
    currentProgress * currentWeight + estimatedProgress * estimatedWeight;
  ```

## UI Components

### Cards

- Consistent styling
- Interactive elements (probability card)
- Responsive layout
- Dark mode support

### Tables

- Responsive design
- Mobile-optimized buttons
- Proper spacing
- Action column management:
  ```css
  .actions-cell {
    white-space: nowrap;
    min-width: 160px;
  }
  ```

### Modals

- Bootstrap modal integration
- Dynamic content loading
- Proper cleanup
- Event handling

## Event Management

### Event Binding

```javascript
class Manager {
  constructor() {
    this.boundEventHandlers = new Map();
    this.bindEvents();
  }

  bindEvents() {
    const handler = () => this.handleEvent();
    this.boundEventHandlers.set("event", handler);
    element.addEventListener("event", handler);
  }

  removeEventListeners() {
    if (this.boundEventHandlers.has("event")) {
      element.removeEventListener(
        "event",
        this.boundEventHandlers.get("event")
      );
    }
    this.boundEventHandlers.clear();
  }
}
```

## API Integration

### Endpoints

- /api/runs.php
- /api/sessions.php
- /api/settings.php
- /api/sessions/active.php
- /api/sessions/complete.php
- /api/sessions/current.php

### Data Flow

1. User interaction
2. API request
3. Response processing
4. UI update
5. Stats recalculation
6. Event rebinding

## Development Tools

### Build Process

- No build step required
- Direct ES6 module usage
- Browser-native features

### Dependencies

- Bootstrap 5.3.0
- Native JavaScript
- PHP Backend
- MySQL Database

## Browser Support

- Modern browsers with ES6 support
- Mobile browser optimization
- Touch event support
- Responsive design support
