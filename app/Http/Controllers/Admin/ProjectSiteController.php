<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectSiteRequest;
use App\Http\Requests\Admin\UpdateProjectSiteRequest;
use App\Models\ProjectSite;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectSiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sites.manage');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/ProjectSites/Index', [
            'sites' => ProjectSite::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ProjectSites/Form', [
            'site' => null,
        ]);
    }

    public function store(StoreProjectSiteRequest $request): RedirectResponse
    {
        ProjectSite::create($request->validated());

        return redirect()->route('admin.project-sites.index')
            ->with('success', 'Project site created.');
    }

    public function edit(ProjectSite $projectSite): Response
    {
        return Inertia::render('Admin/ProjectSites/Form', [
            'site' => $projectSite,
        ]);
    }

    public function update(UpdateProjectSiteRequest $request, ProjectSite $projectSite): RedirectResponse
    {
        $projectSite->update($request->validated());

        return redirect()->route('admin.project-sites.index')
            ->with('success', 'Project site updated.');
    }

    public function destroy(ProjectSite $projectSite): RedirectResponse
    {
        $projectSite->delete();

        return redirect()->route('admin.project-sites.index')
            ->with('success', 'Project site deleted.');
    }
}
