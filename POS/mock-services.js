// Mock Services for Frontend-Only Demo
// This file simulates all backend functionality with localStorage and in-memory data

class MockProductsService {
    static mockProducts = [
        // Hot Drinks
        { id: 1, name: "Espresso", description: "Rich and bold single shot of espresso", price: 89, category: "hot", image_url: "https://images.unsplash.com/photo-1541167760496-1628856ab772?w=400" },
        { id: 2, name: "Cappuccino", description: "Espresso with steamed milk foam", price: 120, category: "hot", image_url: "https://images.unsplash.com/photo-1534790566855-4cb5c5d9a0f1?w=400" },
        { id: 3, name: "Latte", description: "Smooth espresso with steamed milk", price: 135, category: "hot", image_url: "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400" },
        { id: 4, name: "Americano", description: "Espresso with hot water", price: 105, category: "hot", image_url: "https://images.unsplash.com/photo-1534607999059-5e2d4d91a4b2?w=400" },
        { id: 5, name: "Mocha", description: "Chocolate espresso with steamed milk", price: 145, category: "hot", image_url: "https://images.unsplash.com/photo-1485902336522-3dc13547e4d4?w=400" },
        { id: 6, name: "Hot Chocolate", description: "Rich chocolate drink with marshmallows", price: 110, category: "hot", image_url: "https://images.unsplash.com/photo-1542990233-715c2c46585a?w=400" },
        
        // Cold Drinks
        { id: 7, name: "Iced Coffee", description: "Cold brew over ice", price: 95, category: "cold", image_url: "https://images.unsplash.com/photo-1517701550937-3bc5abaae487?w=400" },
        { id: 8, name: "Iced Latte", description: "Espresso with cold milk over ice", price: 125, category: "cold", image_url: "https://images.unsplash.com/photo-1497515114629-f71d768fd07c?w=400" },
        { id: 9, name: "Cold Brew", description: "12-hour steeped coffee concentrate", price: 140, category: "cold", image_url: "https://images.unsplash.com/photo-1551030272-0bdc0b1a8dba?w=400" },
        { id: 10, name: "Frappuccino", description: "Blended coffee drink with ice", price: 155, category: "cold", image_url: "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400" },
        { id: 11, name: "Iced Tea", description: "Refreshing cold tea with lemon", price: 75, category: "cold", image_url: "https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400" },
        { id: 12, name: "Smoothie", description: "Mixed berry smoothie", price: 130, category: "cold", image_url: "https://images.unsplash.com/photo-1502741338009-cac2772e18bc?w=400" },
        
        // Pastries
        { id: 13, name: "Croissant", description: "Buttery French croissant", price: 65, category: "pastries", image_url: "https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400" },
        { id: 14, name: "Muffin", description: "Fresh blueberry muffin", price: 55, category: "pastries", image_url: "https://images.unsplash.com/photo-1607305387299-a3d9611cd469?w=400" },
        { id: 15, name: "Bagel", description: "Toasted bagel with cream cheese", price: 70, category: "pastries", image_url: "https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400" },
        { id: 16, name: "Danish", description: "Sweet pastry with fruit filling", price: 75, category: "pastries", image_url: "https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400" },
        { id: 17, name: "Cinnamon Roll", description: "Warm cinnamon roll with icing", price: 85, category: "pastries", image_url: "https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400" },
        { id: 18, name: "Cookie", description: "Chocolate chip cookie", price: 45, category: "pastries", image_url: "https://images.unsplash.com/photo-1499636136210-6f4ee915d63c?w=400" },
        
        // Merchandise
        { id: 19, name: "Coffee Mug", description: "Ceramic Legend Brews mug", price: 250, category: "merch", image_url: "https://images.unsplash.com/photo-1514228742587-6b1558fcf93a?w=400" },
        { id: 20, name: "T-Shirt", description: "Premium cotton t-shirt", price: 450, category: "merch", image_url: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400" },
        { id: 21, name: "Coffee Beans", description: "Premium arabica beans 500g", price: 380, category: "merch", image_url: "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400" },
        { id: 22, name: "Travel Tumbler", description: "Insulated travel mug", price: 320, category: "merch", image_url: "https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400" },
        { id: 23, name: "Apron", description: "Barista apron", price: 280, category: "merch", image_url: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400" },
        { id: 24, name: "Coffee Maker", description: "Home brewing kit", price: 1200, category: "merch", image_url: "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400" }
    ];

    static async getProducts(category = 'all') {
        await this.simulateDelay(800);
        
        if (category === 'all') {
            return [...this.mockProducts];
        }
        return this.mockProducts.filter(product => product.category === category);
    }

    static async searchProducts(searchTerm) {
        await this.simulateDelay(600);
        const term = searchTerm.toLowerCase();
        return this.mockProducts.filter(product => 
            product.name.toLowerCase().includes(term) ||
            product.description.toLowerCase().includes(term)
        );
    }

    static async getProductById(id) {
        await this.simulateDelay(300);
        return this.mockProducts.find(product => product.id === parseInt(id));
    }

    static simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

class MockAuthService {
    static mockUsers = [
        { id: 1, username: "admin", password: "admin", role: "admin", email: "admin@legendbrews.com", created_at: "2024-01-01" },
        { id: 2, username: "user", password: "user", role: "customer", email: "user@legendbrews.com", created_at: "2024-01-02" },
        { id: 3, username: "john", password: "123", role: "customer", email: "john@example.com", created_at: "2024-01-03" },
        { id: 4, username: "jane", password: "123", role: "customer", email: "jane@example.com", created_at: "2024-01-04" }
    ];

    static async login(username, password) {
        await this.simulateDelay(500);
        
        // Demo mode: accept any credentials
        if (username && password) {
            // Check if user exists in mock data
            let user = this.mockUsers.find(u => u.username === username && u.password === password);
            
            // If not found, create a new user (demo mode)
            if (!user) {
                user = {
                    id: Date.now(),
                    username: username,
                    role: username.includes('admin') ? 'admin' : 'customer',
                    email: `${username}@legendbrews.com`,
                    created_at: new Date().toISOString().split('T')[0]
                };
            }
            
            return { success: true, user };
        }
        
        return { success: false, error: "Username and password are required" };
    }

    static async register(username, password, confirmPassword) {
        await this.simulateDelay(700);
        
        // Validation
        if (!username || !password) {
            return { success: false, error: "Username and password are required" };
        }
        
        if (password.length < 6) {
            return { success: false, error: "Password must be at least 6 characters" };
        }
        
        if (password !== confirmPassword) {
            return { success: false, error: "Passwords do not match" };
        }
        
        // Check if username already exists
        if (this.mockUsers.find(u => u.username === username)) {
            return { success: false, error: "Username already exists" };
        }
        
        // Create new user
        const newUser = {
            id: Date.now(),
            username: username,
            password: password,
            role: 'customer',
            email: `${username}@legendbrews.com`,
            created_at: new Date().toISOString().split('T')[0]
        };
        
        this.mockUsers.push(newUser);
        return { success: true, user: newUser };
    }

    static simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

class MockOrdersService {
    static generateMockOrders() {
        const orders = [];
        const statuses = ['completed', 'processing', 'pending'];
        const users = JSON.parse(localStorage.getItem('allUsers') || '[]');
        
        for (let i = 1; i <= 50; i++) {
            const orderDate = new Date();
            orderDate.setDate(orderDate.getDate() - Math.floor(Math.random() * 30));
            
            const items = [];
            const itemCount = Math.floor(Math.random() * 4) + 1;
            for (let j = 0; j < itemCount; j++) {
                const product = MockProductsService.mockProducts[Math.floor(Math.random() * MockProductsService.mockProducts.length)];
                items.push({
                    product_id: product.id,
                    product_name: product.name,
                    quantity: Math.floor(Math.random() * 3) + 1,
                    price: product.price
                });
            }
            
            const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const total = subtotal + 70; // delivery + service fees
            
            orders.push({
                id: i,
                user_id: users.length > 0 ? users[Math.floor(Math.random() * users.length)].id : 1,
                items: items,
                subtotal: subtotal,
                delivery_fee: 50,
                service_fee: 20,
                total: total,
                status: statuses[Math.floor(Math.random() * statuses.length)],
                created_at: orderDate.toISOString(),
                completed_at: orderDate.toISOString()
            });
        }
        
        return orders;
    }

    static async getOrders(userId = null) {
        await this.simulateDelay(600);
        
        let orders = this.generateMockOrders();
        
        if (userId) {
            orders = orders.filter(order => order.user_id === userId);
        }
        
        return orders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }

    static async createOrder(userId, cartItems, total) {
        await this.simulateDelay(1000);
        
        const order = {
            id: Date.now(),
            user_id: userId,
            items: cartItems.map(item => ({
                product_id: item.id,
                product_name: item.name,
                quantity: item.quantity,
                price: item.price
            })),
            subtotal: total - 70,
            delivery_fee: 50,
            service_fee: 20,
            total: total,
            status: 'processing',
            created_at: new Date().toISOString(),
            completed_at: new Date().toISOString()
        };
        
        // Save to localStorage for persistence
        const existingOrders = JSON.parse(localStorage.getItem('orders') || '[]');
        existingOrders.push(order);
        localStorage.setItem('orders', JSON.stringify(existingOrders));
        
        return { success: true, order };
    }

    static simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

class MockAnalyticsService {
    static async getDashboardStats() {
        await this.simulateDelay(500);
        
        const orders = MockOrdersService.generateMockOrders();
        const completedOrders = orders.filter(o => o.status === 'completed');
        
        return {
            total_products: MockProductsService.mockProducts.length,
            total_orders: completedOrders.length,
            total_revenue: completedOrders.reduce((sum, order) => sum + order.total, 0),
            today_orders: completedOrders.filter(o => {
                const today = new Date().toDateString();
                return new Date(o.created_at).toDateString() === today;
            }).length,
            today_revenue: completedOrders.filter(o => {
                const today = new Date().toDateString();
                return new Date(o.created_at).toDateString() === today;
            }).reduce((sum, order) => sum + order.total, 0),
            low_stock: Math.floor(Math.random() * 10) + 5
        };
    }

    static async getSalesData(period = 'week') {
        await this.simulateDelay(400);
        
        const data = [];
        const days = period === 'week' ? 7 : 30;
        
        for (let i = days - 1; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            
            data.push({
                date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                orders: Math.floor(Math.random() * 50) + 10,
                revenue: Math.floor(Math.random() * 5000) + 1000
            });
        }
        
        return data;
    }

    static async getCategorySales() {
        await this.simulateDelay(300);
        
        // Generate category sales data like the PHP query
        const categories = ['hot', 'cold', 'pastries', 'merch'];
        return categories.map(category => {
            const categoryProducts = MockProductsService.mockProducts.filter(p => p.category === category);
            const totalQuantity = Math.floor(Math.random() * 200) + 50;
            const totalRevenue = totalQuantity * (Math.random() * 100 + 50);
            
            return {
                category: category,
                total_quantity: totalQuantity,
                total_revenue: totalRevenue
            };
        });
    }

    static async getTopProducts(limit = 5) {
        await this.simulateDelay(300);
        
        return MockProductsService.mockProducts.slice(0, limit).map(product => ({
            name: product.name,
            price: product.price,
            sold_count: Math.floor(Math.random() * 100) + 20,
            total_quantity: Math.floor(Math.random() * 200) + 50
        }));
    }

    static async getRecentOrders(limit = 10) {
        await this.simulateDelay(400);
        
        const orders = MockOrdersService.generateMockOrders();
        return orders.slice(0, limit).map(order => ({
            id: order.id,
            total: order.total,
            created_at: order.created_at,
            item_count: order.items.length
        }));
    }

    static simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

class MockCustomersService {
    static async getCustomers() {
        await this.simulateDelay(600);
        
        const users = JSON.parse(localStorage.getItem('allUsers') || '[]');
        const orders = MockOrdersService.generateMockOrders();
        
        return users.map(user => {
            const userOrders = orders.filter(o => o.user_id === user.id);
            const totalSpent = userOrders.reduce((sum, order) => sum + order.total, 0);
            
            return {
                ...user,
                total_orders: userOrders.length,
                total_spent: totalSpent,
                last_order: userOrders.length > 0 ? userOrders[0].created_at : null
            };
        });
    }

    static simulateDelay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        MockProductsService,
        MockAuthService,
        MockOrdersService,
        MockAnalyticsService,
        MockCustomersService
    };
}
