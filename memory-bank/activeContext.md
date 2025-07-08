# Active Context

## Current Focus

- Implementing session management system
- Improving mobile responsiveness
- Enhancing UI/UX for statistics display

## Recent Changes

### Session Management

- Added session creation and management
- Added past sessions view
- Added session completion functionality
- Added session switching capability
- Added session stats calculation

### UI Improvements

- Added probability info modal
- Added hover effects for probability card
- Fixed mobile layout for action buttons
- Improved table responsiveness
- Added proper spacing and sizing for mobile

### Code Organization

- Split app.js into modules:
  - StatsManager
  - ModalManager
  - UIManager
  - PastSessionsManager
  - SessionManager
  - RunManager
- Improved event handling
- Added proper dependency injection
- Fixed circular dependencies

## Active Decisions

- Using weighted probability calculation (30% current progress, 70% estimated progress)
- Using class-based selectors instead of nth-child for better maintainability
- Using responsive design breakpoints at 991.98px and 375px
- Using proper event cleanup to prevent memory leaks

## Project Insights

- Mobile-first design is crucial for usability
- Clear separation of concerns improves maintainability
- Proper event handling prevents duplicate listeners
- Weighted calculations provide better user motivation
