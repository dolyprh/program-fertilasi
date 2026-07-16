<?php

namespace App\Http\Controllers;

use App\Http\Requests\Master\MasterItemRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MasterDataController extends Controller
{
    /**
     * @return array<string, array{label: string, description: string, model: class-string<Model>}>
     */
    private function types(): array
    {
        return config('master_data');
    }

    /**
     * @return array{label: string, description: string, model: class-string<Model>}
     */
    private function resolveType(string $type): array
    {
        $types = $this->types();

        abort_unless(array_key_exists($type, $types), 404);

        return $types[$type];
    }

    /**
     * @return class-string<Model>
     */
    private function modelClass(string $type): string
    {
        return $this->resolveType($type)['model'];
    }

    public function manage(string $type): Response
    {
        $config = $this->resolveType($type);
        $modelClass = $config['model'];

        return Inertia::render('master-data/Manage', [
            'type' => $type,
            'label' => $config['label'],
            'description' => $config['description'],
            'items' => $modelClass::query()->ordered()->get(),
        ]);
    }

    public function store(MasterItemRequest $request, string $type): RedirectResponse
    {
        $modelClass = $this->modelClass($type);

        $modelClass::query()->create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Data master berhasil ditambahkan.',
        ]);

        return to_route('master-data.manage', $type);
    }

    public function update(MasterItemRequest $request, string $type, int $id): RedirectResponse
    {
        $modelClass = $this->modelClass($type);
        $item = $modelClass::query()->findOrFail($id);

        $item->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Data master berhasil diperbarui.',
        ]);

        return to_route('master-data.manage', $type);
    }

    public function destroy(Request $request, string $type, int $id): RedirectResponse
    {
        $modelClass = $this->modelClass($type);
        $item = $modelClass::query()->findOrFail($id);

        if ($item->classifications()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Data tidak dapat dihapus karena sudah digunakan pada klasifikasi. Nonaktifkan saja.',
            ]);

            return to_route('master-data.manage', $type);
        }

        $item->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Data master berhasil dihapus.',
        ]);

        return to_route('master-data.manage', $type);
    }
}
