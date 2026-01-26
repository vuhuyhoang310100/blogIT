# Hướng Dẫn Sử Dụng `Filterable` Trait

Trait `Filterable` cung cấp khả năng tìm kiếm (`search`) và lọc (`filter`) dữ liệu linh hoạt cho Eloquent Models, bao gồm hỗ trợ **Deep Nested Relationships** (quan hệ lồng nhau nhiều cấp) và các **Toán tử so sánh** tùy chỉnh.

## 1. Cài Đặt
Nhúng trait vào Model và khai báo property `$searchable` nếu muốn sử dụng tính năng tìm kiếm nhanh.

```php
namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    /**
     * Các trường cho phép tìm kiếm qua scopeSearch (LIKE %keyword%)
     */
    protected $searchable = [
        'name', 
        'email', 
        'phone_number'
    ];
    
    // ... relations
}
```

## 2. Các Tính Năng

### A. Search (Tìm kiếm từ khóa)
Tìm kiếm chuỗi ký tự trên tất cả các cột được định nghĩa trong `$searchable`.

**Sử dụng:**
```php
// Tìm kiếm 'John' trong name, email hoặc phone_number
$users = User::search('John')->get();
```

---

### B. Filter (Bộ lọc nâng cao)
Hàm `scopeFilter` nhận vào một mảng `$filters` (key-value).

#### 1. Lọc chính xác (Basic Equality)
Mặc định so sánh bằng (`=`).

```php
$filters = [
    'status' => 'active',
    'role'   => 'admin',
];

// Query: WHERE status = 'active' AND role = 'admin'
$users = User::filter($filters)->get();
```

#### 2. Lọc với Toán tử (Operators)
Hỗ trợ các toán tử như `>`, `<`, `>=`, `<=`, `LIKE`, `<>`, v.v.
Cấu trúc value: `['operator' => '...', 'value' => '...']`

```php
$filters = [
    'age' => [
        'operator' => '>=',
        'value'    => 18
    ],
    'name' => [
        'operator' => 'LIKE',
        'value'    => '%Nguyen%'
    ]
];

// Query: WHERE age >= 18 AND name LIKE '%Nguyen%'
$users = User::filter($filters)->get();
```

#### 3. Lọc theo Relationship (Deep Nesting supported)
Tự động query vào các bảng liên kết thông qua dấu chấm `.`. Hỗ trợ relation nhiều cấp độ.

```php
$filters = [
    // Lọc theo quan hệ trực tiếp (User belongTo Department)
    'department.name' => 'IT',

    // Lọc sâu nhiều cấp (Post -> Author -> Profile -> City)
    'author.profile.city' => 'Ha Noi'
];

// Query: whereHas('department', ...) AND whereHas('author.profile', ...)
$posts = Post::filter($filters)->get();
```

#### 4. Lọc Relationship kèm Toán tử
Kết hợp sức mạnh của cả hai loại trên.

```php
$filters = [
    // Lấy user có bài viết trên 1000 views
    'posts.views' => [
        'operator' => '>',
        'value'    => 1000
    ]
];

// Query: SELECT * FROM users WHERE EXISTS (SELECT * FROM posts WHERE user_id = users.id AND views > 1000)
$users = User::filter($filters)->get();
```

## 3. Ví Dụ Thực Tế Trong Controller

Kết hợp với `$request->all()` hoặc `$request->only()` để tạo API lọc linh hoạt.

```php
public function index(Request $request)
{
    // 1. Lấy các tham số filter từ request
    // Ví dụ URL: /users?role=admin&department.name=IT&age[operator]=>&age[value]=18
    $filters = $request->only([
        'role', 
        'status', 
        'department.name', 
        'age',
        'posts.views' // Filter theo nested relation
    ]);

    // 2. Query
    $data = User::filter($filters)
                ->search($request->query('q')) // ?q=keyword
                ->paginate(20);

    return response()->json($data);
}
```
