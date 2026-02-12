# 📋 Laravel Development Standards - Architecture Guidelines

> **Version:** 1.0  
> **Last Updated:** February 2026  
> **Purpose:** Standar arsitektur untuk pengembangan aplikasi Laravel menggunakan Repository Pattern + Service Layer

---

## 📚 Table of Contents

1. [Arsitektur Aplikasi](#arsitektur-aplikasi)
2. [Prinsip Dasar](#prinsip-dasar)
3. [Standar Penamaan](#standar-penamaan)
4. [Template per Layer](#template-per-layer)
5. [Checklist Pengembangan](#checklist-pengembangan)
6. [Best Practices](#best-practices)
7. [Contoh Penggunaan](#contoh-penggunaan)

---

## 🏛️ Arsitektur Aplikasi

Aplikasi ini menggunakan **Repository Pattern + Service Layer** dengan struktur sebagai berikut:

```
app/
├── Models/              → Eloquent Models
├── Repositories/
│   ├── Contracts/       → Repository Interfaces
│   └── *Repository.php  → Repository Implementations
├── Services/            → Business Logic Layer
├── Http/
│   ├── Controllers/
│   │   ├── Api/        → API Controllers
│   │   └── Web/        → Web Controllers
│   ├── Requests/       → Form Request Validation
│   └── Resources/      → API Response Transformers
└── Providers/
    └── RepositoryServiceProvider.php → Repository Bindings
```

---

## 🎯 Prinsip Dasar

### 1. Separation of Concerns
Setiap layer memiliki tanggung jawab yang jelas:

| Layer | Tanggung Jawab |
|-------|----------------|
| **Controller** | Menerima request, memanggil service, mengembalikan response |
| **Service** | Business logic, orchestration, transaction handling |
| **Repository** | Data access layer, query database |
| **Request** | Validasi input |
| **Resource** | Transform data untuk response |

### 2. Flow Request
```
Request → Controller → Service → Repository → Model → Database
                ↓
            Response ← Resource ← Service ← Repository
```

### 3. Dependency Injection
- Gunakan Interface untuk dependency injection
- Binding dilakukan di RepositoryServiceProvider
- Service depend ke Repository Interface, bukan concrete class

---

## 📝 Standar Penamaan

### Files & Classes
```
Model           : User
Interface       : UserRepositoryInterface
Repository      : UserRepository
Service         : UserService
Controller API  : Api/UserController
Controller Web  : Web/UserController
Request Store   : StoreUserRequest
Request Update  : UpdateUserRequest
Resource        : UserResource
Collection      : UserCollection
```

### Methods Repository
```php
public function all()
public function find($id)
public function create(array $data)
public function update($id, array $data)
public function delete($id)
public function paginate($perPage = 15, array $filters = [])
```

### Methods Service
```php
public function getAll()
public function getById($id)
public function create{Model}(array $data)
public function update{Model}($id, array $data)
public function delete{Model}($id)
public function get{Models}Paginated($perPage = 15, array $filters = [])
```

---

## 🏗️ Template per Layer

### 1. Repository Interface

**Location:** `app/Repositories/Contracts/{Model}RepositoryInterface.php`

```php
<?php

namespace App\Repositories\Contracts;

interface {Model}RepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15, array $filters = []);
}
```

### 2. Repository Implementation

**Location:** `app/Repositories/{Model}Repository.php`

```php
<?php

namespace App\Repositories;

use App\Models\{Model};
use App\Repositories\Contracts\{Model}RepositoryInterface;

class {Model}Repository implements {Model}RepositoryInterface
{
    protected $model;

    public function __construct({Model} $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item->delete();
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->query();

        // Apply filters here
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }
}
```

### 3. Service Layer

**Location:** `app/Services/{Model}Service.php`

```php
<?php

namespace App\Services;

use App\Repositories\Contracts\{Model}RepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class {Model}Service
{
    protected $repository;

    public function __construct({Model}RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $item = $this->repository->create($data);

            // Business logic here
            // - Send notifications
            // - Log activities
            // - Update related data

            Log::info('{Model} created', ['id' => $item->id]);

            DB::commit();

            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create {model}', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal membuat {model}: ' . $e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {
            $item = $this->repository->update($id, $data);

            // Business logic here

            Log::info('{Model} updated', ['id' => $id]);

            DB::commit();

            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update {model}', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal update {model}: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            // Business logic validation
            $item = $this->repository->find($id);
            
            // Check if can delete
            // if ($item->hasRelatedData()) {
            //     throw new \Exception('Cannot delete');
            // }

            $this->repository->delete($id);

            Log::info('{Model} deleted', ['id' => $id]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete {model}', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal hapus {model}: ' . $e->getMessage());
        }
    }
}
```

### 4. API Controller

**Location:** `app/Http/Controllers/Api/{Model}Controller.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{Model}Request;
use App\Http\Requests\Update{Model}Request;
use App\Http\Resources\{Model}Resource;
use App\Http\Resources\{Model}Collection;
use App\Services\{Model}Service;
use Illuminate\Http\Request;

class {Model}Controller extends Controller
{
    protected $service;

    public function __construct({Model}Service $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
            
            $items = $this->service->getPaginated($perPage, $filters);

            return new {Model}Collection($items);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource.
     */
    public function store(Store{Model}Request $request)
    {
        try {
            $item = $this->service->create($request->validated());

            return (new {Model}Resource($item))
                ->additional([
                    'success' => true,
                    'message' => 'Data berhasil dibuat'
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $item = $this->service->getById($id);

            return new {Model}Resource($item);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Update{Model}Request $request, $id)
    {
        try {
            $item = $this->service->update($id, $request->validated());

            return (new {Model}Resource($item))
                ->additional([
                    'success' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 5. Web Controller

**Location:** `app/Http/Controllers/Web/{Model}Controller.php`

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{Model}Request;
use App\Http\Requests\Update{Model}Request;
use App\Services\{Model}Service;
use Illuminate\Http\Request;

class {Model}Controller extends Controller
{
    protected $service;

    public function __construct({Model}Service $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
        
        $items = $this->service->getPaginated($perPage, $filters);

        return view('{models}.index', compact('items', 'filters'));
    }

    public function create()
    {
        return view('{models}.create');
    }

    public function store(Store{Model}Request $request)
    {
        try {
            $item = $this->service->create($request->validated());

            return redirect()
                ->route('{models}.show', $item->id)
                ->with('success', 'Data berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $item = $this->service->getById($id);

            return view('{models}.show', compact('item'));
        } catch (\Exception $e) {
            return redirect()
                ->route('{models}.index')
                ->with('error', 'Data tidak ditemukan');
        }
    }

    public function edit($id)
    {
        try {
            $item = $this->service->getById($id);

            return view('{models}.edit', compact('item'));
        } catch (\Exception $e) {
            return redirect()
                ->route('{models}.index')
                ->with('error', 'Data tidak ditemukan');
        }
    }

    public function update(Update{Model}Request $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());

            return redirect()
                ->route('{models}.show', $id)
                ->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);

            return redirect()
                ->route('{models}.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
```

### 6. Form Request Validation

**Store Request:** `app/Http/Requests/Store{Model}Request.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{Model}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:{models},email',
            'status' => 'nullable|in:active,inactive',
            // Add more rules
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            // Add more messages
        ];
    }
}
```

**Update Request:** `app/Http/Requests/Update{Model}Request.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update{Model}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('{model}');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('{models}')->ignore($id)
            ],
            'status' => 'nullable|in:active,inactive',
            // Add more rules
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            // Add more messages
        ];
    }
}
```

### 7. API Resources

**Resource:** `app/Http/Resources/{Model}Resource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class {Model}Resource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'status_label' => $this->status === 'active' ? 'Aktif' : 'Tidak Aktif',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Conditional attributes
            'extra_data' => $this->when(isset($this->extra_data), $this->extra_data),
        ];
    }
}
```

**Collection:** `app/Http/Resources/{Model}Collection.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class {Model}Collection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'active_count' => $this->collection->where('status', 'active')->count(),
                'inactive_count' => $this->collection->where('status', 'inactive')->count(),
            ],
        ];
    }
}
```

### 8. Repository Service Provider

**Location:** `app/Providers/RepositoryServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings
     */
    protected $repositories = [
        \App\Repositories\Contracts\UserRepositoryInterface::class => 
            \App\Repositories\UserRepository::class,
        
        \App\Repositories\Contracts\ProductRepositoryInterface::class => 
            \App\Repositories\ProductRepository::class,
        
        // Add more repositories here
    ];

    /**
     * Register services.
     */
    public function register()
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
```

**Don't forget to register in `config/app.php`:**

```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

---

## ✅ Checklist Pengembangan Modul Baru

Saat membuat modul baru (contoh: Product), ikuti checklist ini:

- [ ] **1. Migration** - Buat tabel database dengan field yang diperlukan
- [ ] **2. Model** - Buat Eloquent model dengan fillable, casts, relationships
- [ ] **3. Repository Interface** - Buat contract di `Contracts/` folder
- [ ] **4. Repository Implementation** - Implement interface dengan query logic
- [ ] **5. Service** - Buat business logic layer dengan DB transaction
- [ ] **6. Store Request** - Buat validation untuk create operation
- [ ] **7. Update Request** - Buat validation untuk update operation
- [ ] **8. Resource** - Buat transformer untuk single item response
- [ ] **9. Collection** - Buat transformer untuk collection response
- [ ] **10. API Controller** - Buat controller untuk API endpoints
- [ ] **11. Web Controller** - Buat controller untuk web routes
- [ ] **12. Binding** - Daftarkan repository di RepositoryServiceProvider
- [ ] **13. API Routes** - Tambahkan di `routes/api.php`
- [ ] **14. Web Routes** - Tambahkan di `routes/web.php`
- [ ] **15. Views** (optional) - Buat blade templates untuk web interface
- [ ] **16. Tests** - Buat unit & feature tests

---

## 🎯 Best Practices

### 1. Transaction Handling

**ALWAYS use DB transaction** dalam Service layer untuk operasi Create, Update, Delete:

```php
DB::beginTransaction();
try {
    // Operations here
    DB::commit();
    return $result;
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Operation failed', ['error' => $e->getMessage()]);
    throw $e;
}
```

### 2. Logging

Log semua operasi penting untuk audit trail:

```php
// Success
Log::info('User created successfully', [
    'user_id' => $user->id,
    'email' => $user->email,
    'created_by' => auth()->id()
]);

// Error
Log::error('Failed to create user', [
    'error' => $e->getMessage(),
    'data' => $data,
    'trace' => $e->getTraceAsString()
]);
```

### 3. Exception Handling

Throw exception dengan pesan bahasa Indonesia yang jelas:

```php
// ❌ Bad
throw new \Exception('Error');

// ✅ Good
throw new \Exception('Gagal membuat user: Email sudah terdaftar');
throw new \Exception('Tidak dapat menghapus kategori yang masih memiliki produk');
```

### 4. Validation

- Validasi dilakukan di **Form Request**, bukan di Controller atau Service
- Gunakan custom messages dalam **bahasa Indonesia**
- Pisahkan Store dan Update request untuk fleksibilitas

```php
// ✅ Correct
public function store(StoreUserRequest $request)
{
    $user = $this->service->create($request->validated());
}

// ❌ Wrong - Validation di Controller
public function store(Request $request)
{
    $request->validate([...]);
}
```

### 5. Response Format

**API Success Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe"
    },
    "success": true,
    "message": "Data berhasil dibuat"
}
```

**API Error Response:**
```json
{
    "success": false,
    "message": "Gagal membuat data",
    "error": "Email sudah terdaftar"
}
```

**Collection Response:**
```json
{
    "data": [...],
    "meta": {
        "total": 100,
        "per_page": 15,
        "current_page": 1
    },
    "links": {
        "first": "...",
        "next": "..."
    }
}
```

### 6. Query Optimization

```php
// ✅ Eager Loading
$users = User::with('roles', 'permissions')->get();

// ❌ N+1 Problem
$users = User::all();
foreach ($users as $user) {
    echo $user->role->name; // N+1 query!
}
```

### 7. Soft Deletes

Gunakan soft deletes untuk data penting:

```php
// Migration
$table->softDeletes();

// Model
use SoftDeletes;

// Repository
public function delete($id)
{
    $item = $this->find($id);
    return $item->delete(); // Soft delete
}

// Permanent delete (jika diperlukan)
public function forceDelete($id)
{
    $item = $this->find($id);
    return $item->forceDelete();
}
```

### 8. Mass Assignment Protection

```php
// Model
protected $fillable = ['name', 'email', 'phone'];

// Atau
protected $guarded = ['id', 'created_at', 'updated_at'];
```

### 9. Polymorphic Relations (jika diperlukan)

```php
// Example: Comments bisa untuk Post atau Video
public function commentable()
{
    return $this->morphTo();
}
```

### 10. Service Method Naming

```php
// ✅ Clear and descriptive
getUsersByStatus($status)
createUserWithRole($data, $roleId)
updateUserPassword($userId, $newPassword)
sendWelcomeEmailToUser($userId)

// ❌ Vague
getUsers() // Get all or filtered?
update($data) // Update what?
process() // Process what?
```

---

## 🔄 Workflow Diagram

```
┌─────────────┐
│   Request   │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│   Controller    │ ◄── Inject Service
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│ Form Request    │ ◄── Validate Input
│  (Validation)   │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│    Service      │ ◄── Inject Repository Interface
│ (Business Logic)│
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Repository    │ ◄── Data Access
│  (Query Logic)  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│      Model      │ ◄── Eloquent ORM
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│    Database     │
└─────────────────┘
```

---

## 📖 Contoh Penggunaan Standar

### Request ke AI/Developer:

```
Buatkan modul CRUD untuk Product dengan standar arsitektur yang sudah ditentukan.

Requirements:
- Model: Product (name, description, price, stock, category_id, status)
- Fitur: CRUD, search by name, filter by category, update stock
- Validation: 
  * name required max 255
  * price numeric min 0
  * stock integer min 0
  * category_id exists in categories table
- Relationship: belongsTo Category
- Include API & Web controllers
- Gunakan Repository Pattern + Service Layer
- Response format menggunakan Resource & Collection

Ikuti template dan best practices yang sudah ditetapkan.
```

### Response yang Diharapkan:

Developer/AI akan generate:
1. Migration file
2. Product model dengan relationship
3. ProductRepositoryInterface
4. ProductRepository implementation
5. ProductService dengan business logic
6. StoreProductRequest & UpdateProductRequest
7. ProductResource & ProductCollection
8. Api/ProductController
9. Web/ProductController
10. Binding di RepositoryServiceProvider
11. Routes untuk API & Web

Semua mengikuti standar yang sudah ditetapkan.

---

## 🔧 Generator Command (Optional)

Untuk mempercepat development, bisa buat artisan command:

```bash
php artisan make:module Product
```

Command ini akan generate semua file yang diperlukan sesuai standar.

**Contoh Implementation:**

```php
// app/Console/Commands/MakeModuleCommand.php
php artisan make:command MakeModuleCommand

// Generate:
// - Migration
// - Model
// - Repository Interface & Implementation
// - Service
// - Requests (Store & Update)
// - Resources (Resource & Collection)
// - Controllers (API & Web)
// - Auto register binding
```

---

## 📚 Additional Resources

### Dokumentasi Laravel
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Service Container](https://laravel.com/docs/container)
- [Validation](https://laravel.com/docs/validation)
- [API Resources](https://laravel.com/docs/eloquent-resources)

### Design Patterns
- Repository Pattern
- Service Layer Pattern
- Dependency Injection
- SOLID Principles

---

## 📝 Notes

- Standar ini bersifat **guideline**, bisa disesuaikan dengan kebutuhan project
- Untuk project kecil, bisa simplified (tanpa Repository pattern)
- Untuk project besar, pertimbangkan tambahan: Events, Jobs, Observers, DTOs
- Selalu update dokumentasi ini seiring perkembangan project

---

## 📞 Support

Jika ada pertanyaan atau saran perbaikan standar ini:
- Buat issue di repository
- Diskusikan dengan tim
- Update dokumentasi setelah ada kesepakatan

---

**Version History:**
- v1.0 (Feb 2026) - Initial release

**Maintained by:** [Team Name]
**Last Review:** [Date]
