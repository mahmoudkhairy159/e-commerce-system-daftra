# E-Commerce React Frontend

A modern, responsive React.js frontend for the Laravel e-commerce system built with Material-UI.

## Features

- **Clean, responsive design** using Material-UI components
- **React Router** for client-side routing
- **Authentication system** with JWT token management
- **Shopping cart functionality** with real-time updates
- **Product catalog** with search and filtering
- **Order management** with detailed order tracking
- **Context-based state management** for auth and cart
- **REST API integration** with the Laravel backend

## Tech Stack

- React 18.2.0
- Material-UI 5.14.0
- React Router 6.14.0
- Axios for API calls
- Notistack for notifications
- Vite for build tooling

## Project Structure

```
resources/js/
├── App.jsx                 # Main app component with routing
├── app.jsx                 # Entry point
├── components/
│   ├── Layout/
│   │   └── Layout.jsx      # Main layout with header and navigation
│   └── ProtectedRoute.jsx  # Route protection component
├── contexts/
│   ├── AuthContext.jsx     # Authentication state management
│   └── CartContext.jsx     # Shopping cart state management
├── pages/
│   ├── LoginPage.jsx       # User login page
│   ├── ProductsPage.jsx    # Product listing and filtering
│   ├── ProductDetailsPage.jsx # Individual product details
│   ├── CartPage.jsx        # Shopping cart management
│   └── OrderDetailsPage.jsx   # Order details and tracking
└── services/
    └── apiService.js       # API service layer
```

## Available Pages

### 1. Login Page (`/login`)
- Clean login form with email/password
- JWT token authentication
- Automatic redirect after successful login
- Error handling and validation

### 2. Products Page (`/products`)
- Product grid with responsive design
- Search functionality
- Category filtering
- Price range filtering
- Add to cart functionality
- Combined with order management features

### 3. Product Details Page (`/products/:slug`)
- Detailed product information
- Image display
- Quantity selection
- Add to cart with custom quantity
- Product reviews display
- Stock availability

### 4. Cart Page (`/cart`)
- Cart items management
- Quantity updates
- Item removal
- Order summary with taxes and shipping
- Proceed to checkout functionality
- Clear cart option

### 5. Order Details Page (`/orders/:id`)
- Order status tracking with stepper
- Order items table
- Order summary
- Shipping information
- Order progress visualization

## API Integration

The frontend communicates with the Laravel backend through REST API endpoints:

### Authentication
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `GET /api/v1/users/get` - Get user info

### Products
- `GET /api/v1/products` - List products
- `GET /api/v1/products/{id}` - Get product details
- `GET /api/v1/products/slugs/{slug}` - Get product by slug

### Cart
- `GET /api/v1/cart/cart-products` - Get cart items
- `POST /api/v1/cart/cart-products` - Add to cart
- `PUT /api/v1/cart/cart-products/{id}` - Update cart item
- `DELETE /api/v1/cart/cart-products/{id}` - Remove from cart
- `DELETE /api/v1/cart/clear` - Clear cart

### Orders
- `GET /api/v1/orders` - List orders
- `GET /api/v1/orders/{id}` - Get order details
- `POST /api/v1/orders` - Create order

## Installation & Setup

1. **Install Dependencies**
   ```bash
   npm install
   ```

2. **Start Development Server**
   ```bash
   npm run dev
   ```

3. **Build for Production**
   ```bash
   npm run build
   ```

## Configuration

The application is configured to work with the Laravel backend:

- **API Base URL**: `/api/v1`
- **Authentication**: JWT tokens stored in localStorage
- **CSRF Protection**: Handled automatically by Laravel
- **Routing**: React Router with fallback to Laravel routes

## Key Features

### Responsive Design
- Mobile-first approach
- Breakpoints for mobile, tablet, and desktop
- Touch-friendly interface
- Optimized for various screen sizes

### State Management
- **AuthContext**: Manages user authentication state
- **CartContext**: Handles shopping cart operations
- Real-time cart updates across components
- Persistent authentication state

### Error Handling
- API error interceptors
- User-friendly error messages
- Automatic token refresh
- Graceful fallbacks

### User Experience
- Loading states for all async operations
- Success/error notifications
- Intuitive navigation
- Clean, modern interface

## Development

### File Organization
- Components are organized by feature
- Shared utilities in services folder
- Context providers for global state
- Clean separation of concerns

### Styling
- Material-UI theme customization
- Consistent design language
- Accessible components
- Dark/light mode support (configurable)

### Performance
- Code splitting with React Router
- Optimized bundle size
- Efficient re-rendering
- Lazy loading where appropriate

## Usage Examples

### Adding to Cart
```javascript
import { useCart } from '../contexts/CartContext';

const { addToCart } = useCart();
await addToCart(productId, quantity);
```

### Authentication Check
```javascript
import { useAuth } from '../contexts/AuthContext';

const { isAuthenticated, user } = useAuth();
```

### API Calls
```javascript
import apiService from '../services/apiService';

const products = await apiService.getProducts();
const product = await apiService.getProduct(id);
```

## Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Contributing
1. Follow React best practices
2. Use functional components and hooks
3. Implement proper error handling
4. Write clean, self-documenting code
5. Maintain responsive design principles

## Troubleshooting

### Common Issues
1. **API Connection**: Ensure Laravel backend is running
2. **CORS Issues**: Check Laravel CORS configuration
3. **Authentication**: Verify JWT token configuration
4. **Build Errors**: Check for missing dependencies

### Development Tips
- Use React Developer Tools for debugging
- Check browser console for API errors
- Verify network requests in DevTools
- Test responsive design on multiple devices

This React frontend provides a complete e-commerce user interface that seamlessly integrates with the Laravel backend, offering a modern and intuitive shopping experience. 
