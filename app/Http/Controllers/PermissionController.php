<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\IndexPermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService) {}

    public function index(IndexPermissionRequest $request): Response
    {
        $dto = $request->toQueryDTO();
        $permissions = $this->permissionService->getAll($dto);

        return Inertia::render('permissions/index', [
            'permissions' => $permissions,
            'filters' => $dto->filters,
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissionService->store($request->validated());

        return to_route('permissions.index')->with('message', 'Permission created successfully.');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update($permission->id, $request->validated());

        return to_route('permissions.index')->with('message', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissionService->delete($permission->id);

        return to_route('permissions.index')->with('message', 'Permission deleted successfully.');
    }
}
