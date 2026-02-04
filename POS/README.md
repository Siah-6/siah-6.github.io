# Legend Brews - Frontend Demo

A fully functional frontend-only demo of the Legend Brews coffee shop management system, converted from PHP to HTML/CSS/JavaScript with mock backend services.

## 🎯 Demo Features

### Authentication (Demo Mode)
- **Login**: ANY credentials work (username/password)
- **Registration**: Any username/password (min 6 chars)
- **Roles**: User and Admin access
- **Session Management**: localStorage persistence
- **Auto-login**: After registration

### User Features
- **Product Browsing**: Categories, search, filtering
- **Shopping Cart**: Add/remove items, quantity management
- **Checkout**: Complete order process
- **Order History**: View past orders
- **Profile Management**: Edit personal information

### Admin Features
- **Dashboard**: Sales analytics, statistics, charts
- **Products Management**: CRUD operations
- **Orders Management**: View all orders, status tracking
- **Customers Management**: User statistics and data
- **Analytics**: Sales and customer analytics
- **Inventory**: Stock management interface

## 🚀 Quick Start

1. **Open `index.html`** in your browser
2. **Login** with any credentials:
   - User: `user` / `user`
   - Admin: `admin` / `admin`
   - Or ANY username/password combination
3. **Explore** all features - everything works!

## 📁 File Structure

```
A06/
├── index.html              # Main shop page
├── login.html              # Login page
├── register.html           # Registration page
├── checkout.html           # Checkout process
├── orders.html             # User order history
├── profile.html            # User profile
├── logout.html             # Logout page
├── mock-services.js        # Mock backend services
├── admin/
│   ├── dashboard.html       # Admin dashboard
│   ├── products.html       # Product management
│   ├── orders.html         # Order management
│   ├── customers.html      # Customer management
│   ├── analytics.html      # Analytics dashboard
│   └── inventory.html      # Inventory management
└── README.md               # This file
```

## 🔧 Technical Implementation

### Mock Services
- **MockProductsService**: Product catalog with 24 items
- **MockAuthService**: User authentication and registration
- **MockOrdersService**: Order management and tracking
- **MockAnalyticsService**: Dashboard statistics and charts
- **MockCustomersService**: Customer data management

### Data Persistence
- **localStorage**: Session management and user data
- **Mock Data**: Realistic product catalog and sample orders
- **State Management**: Cart persistence across sessions

### UI/UX Preservation
- **Exact Design**: All original styling maintained
- **Responsive**: Mobile-friendly layout preserved
- **Interactions**: All buttons, forms, navigation functional
- **Animations**: Smooth transitions and loading states

## 🎨 Design System

### Colors
- Primary: `#2c1810` (Coffee Dark)
- Secondary: `#6f4e37` (Coffee Medium)
- Accent: `#a67c52` (Coffee Light)
- Background: `#f5e6d3` (Coffee Cream)

### Typography
- Headings: 'Playfair Display' serif
- Body: 'Inter' sans-serif
- Icons: Font Awesome 6.4.0

### Components
- Cards, buttons, forms with coffee-themed styling
- Responsive grid layouts
- Interactive hover states and transitions

## 📊 Demo Data

### Products (24 items)
- **Hot Drinks**: Espresso, Cappuccino, Latte, etc.
- **Cold Drinks**: Iced Coffee, Cold Brew, Smoothies
- **Pastries**: Croissants, Muffins, Cookies
- **Merchandise**: Mugs, T-shirts, Coffee beans

### Users
- Pre-configured admin and customer accounts
- Dynamic user creation during registration
- Role-based access control

### Orders
- Sample order history for realistic demo
- Dynamic order creation from cart
- Order status tracking

## 🔐 Security Notes

**This is a DEMO application with intentionally simplified security:**

- No real password validation
- No server-side authentication
- No data encryption
- Mock payment processing

**For production use, implement:**
- Proper authentication system
- Server-side validation
- Secure payment processing
- Data encryption

## 🌟 Key Features Demonstrated

### Frontend Development
- Modern HTML5/CSS3/JavaScript
- Responsive web design
- Component-based architecture
- State management patterns

### User Experience
- Intuitive navigation
- Real-time updates
- Loading states and feedback
- Error handling

### Admin Functionality
- Comprehensive dashboard
- Data visualization with Chart.js
- CRUD operations
- Analytics and reporting

## 🚀 Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 📱 Mobile Responsive

All pages are fully responsive and work seamlessly on:
- Desktop computers
- Tablets
- Mobile phones

## 🎯 Success Criteria Met

✅ **Authentication**: Any credentials work, demo mode functional  
✅ **Roles**: User and Admin access with proper routing  
✅ **Features**: Every button, form, navigation item works  
✅ **Data**: Mock services with realistic data and persistence  
✅ **UI**: Original design exactly preserved  
✅ **Functionality**: Complete shopping and admin workflow  
✅ **Experience**: Professional demo ready for recruiters  

---

**Enjoy exploring the Legend Brews demo!** ☕
