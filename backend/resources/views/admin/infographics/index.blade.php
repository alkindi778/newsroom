@extends('admin.layouts.app')

@section('title', 'إدارة الإنفوجرافيك')
@section('page-title', 'إدارة الإنفوجرافيك')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الإنفوجرافيك</h1>
        <a href="{{ route('admin.infographics.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة إنفوجرافيك جديد
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Infographics Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الإنفوجرافيكات</h6>
        </div>
        <div class="card-body">
            @if($infographics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="100">الصورة</th>
                                <th>العنوان</th>
                                <th>القسم</th>
                                <th width="100">المشاهدات</th>
                                <th width="80">الحالة</th>
                                <th width="80">مميز</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($infographics as $infographic)
                                <tr>
                                    <td>{{ $infographic->id }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $infographic->image) }}" 
                                             alt="{{ $infographic->title }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 80px; max-height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $infographic->title }}</strong>
                                        @if($infographic->title_en)
                                            <br><small class="text-muted">{{ $infographic->title_en }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($infographic->category)
                                            <span class="badge badge-info">{{ $infographic->category->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $infographic->views }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.infographics.toggle-status', $infographic) }}" 
                                              method="POST" 
                                              style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $infographic->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $infographic->is_active ? 'نشط' : 'معطل' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.infographics.toggle-featured', $infographic) }}" 
                                              method="POST" 
                                              style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $infographic->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.infographics.edit', $infographic) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.infographics.destroy', $infographic) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الإنفوجرافيك؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $infographics->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>لا توجد إنفوجرافيكات بعد</h5>
                    <p>يمكنك إضافة إنفوجرافيك جديد من الزر في الأعلى</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
