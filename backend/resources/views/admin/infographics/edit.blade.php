@extends('admin.layouts.app')

@section('title', 'تعديل إنفوجرافيك')
@section('page-title', 'تعديل إنفوجرافيك')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">تعديل: {{ $infographic->title }}</h1>
                <a href="{{ route('admin.infographics.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.infographics.update', $infographic) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- Current Image Preview -->
                        <div class="form-group">
                            <label>الصورة الحالية</label>
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $infographic->image) }}" 
                                     alt="{{ $infographic->title }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 300px;">
                            </div>
                        </div>

                        <!-- Title (Arabic) -->
                        <div class="form-group">
                            <label for="title">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $infographic->title) }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title (English) -->
                        <div class="form-group">
                            <label for="title_en">العنوان (English)</label>
                            <input type="text" 
                                   class="form-control @error('title_en') is-invalid @enderror" 
                                   id="title_en" 
                                   name="title_en" 
                                   value="{{ old('title_en', $infographic->title_en) }}">
                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (Arabic) -->
                        <div class="form-group">
                            <label for="description">الوصف (عربي)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $infographic->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (English) -->
                        <div class="form-group">
                            <label for="description_en">الوصف (English)</label>
                            <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                      id="description_en" 
                                      name="description_en" 
                                      rows="3">{{ old('description_en', $infographic->description_en) }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Image (Optional) -->
                        <div class="form-group">
                            <label for="image">صورة جديدة (اختياري)</label>
                            <input type="file" 
                                   class="form-control-file @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">اترك فارغاً للإبقاء على الصورة الحالية</small>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category_id">القسم</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id">
                                <option value="">-- اختر قسم --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $infographic->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="form-group">
                            <label for="slug">الرابط (Slug)</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $infographic->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div class="form-group">
                            <label for="order">الترتيب</label>
                            <input type="number" 
                                   class="form-control @error('order') is-invalid @enderror" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', $infographic->order) }}" 
                                   min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label for="tags">الوسوم (Tags)</label>
                            <input type="text" 
                                   class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags', is_array($infographic->tags) ? implode(', ', $infographic->tags) : '') }}" 
                                   placeholder="افصل بينها بفاصلة">
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Checkboxes -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $infographic->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">نشط</label>
                            </div>

                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_featured" 
                                       name="is_featured" 
                                       value="1" 
                                       {{ old('is_featured', $infographic->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_featured">مميز</label>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="alert alert-info">
                            <strong>إحصائيات:</strong> 
                            المشاهدات: {{ $infographic->views }} | 
                            تاريخ الإنشاء: {{ $infographic->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> تحديث
                        </button>
                        <a href="{{ route('admin.infographics.index') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
