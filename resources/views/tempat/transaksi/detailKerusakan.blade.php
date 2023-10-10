@extends('layouts.sidebarNavbar_tempat')

@section('content')
<style>
    .square-image-container {
        width: 110px; /* dari 90px menjadi 110px */
        height: 110px; /* dari 90px menjadi 110px */
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .square-image-container img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: block;
        font-size: 18px; /* dari 16px menjadi 18px */
    }
    
    .card.tiny-card {
        width: 190px; /* dari 170px menjadi 190px */
        height: 70px; /* dari 60px menjadi 70px */
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        .square-image-container {
            width: 100px; /* dari 55px menjadi 70px */
            height: 100px; /* dari 55px menjadi 70px */
        }
        .card.tiny-card {
            width: 180px; /* dari 140px menjadi 160px */
        }
    }
    .card-form {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    </style>
@include("layouts.message")
<div class="container mt-5 mb-5 p-4 rounded">
    <form action="/tempat/kerusakan/ajukanKerusakan" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-template card-form">
            <div class="d-flex justify-content-center">
                <h3 class="text-center mb-5">Ajukan Kerusakan Alat</h3>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 col-12 mt-2">
                    <h6>Pilih Alat Olahraga yang Rusak</h6>
                </div>
                <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3 d-flex flex-wrap">
                    @foreach ($dtrans as $item)
                        @php
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$item->fk_id_alat)->get()->first();
                            $dataFileAlat = DB::table('files_alat')->where("fk_id_alat","=",$item->fk_id_alat)->get()->first();
                        @endphp
                        <div class="custom-radio-container">
                            <input type="radio" class="btn-check" name="rusak" id="alat-{{$dataAlat->id_alat}}-{{$item->id_dtrans}}" autocomplete="off" value="{{$dataAlat->id_alat}}-{{$item->id_dtrans}}">
                            <label class="btn btn-outline-primary" for="alat-{{$dataAlat->id_alat}}-{{$item->id_dtrans}}">
                                <div class="card tiny-card h-70 mb-1 mr-1">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Gambar Alat -->
                                            <div class="col-4">
                                                <div class="square-image-container2">
                                                    <img src="{{ asset('upload/' . $dataFileAlat->nama_file_alat) }}" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            
                                            <!-- Nama Alat -->
                                            <div class="col-8 d-flex align-items-center justify-content-between">
                                                <h5 class="card-title truncate-text">{{$dataAlat->nama_alat}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 col-12 mt-2">
                    <h6>Apakah terdapat unsur kesengajaan dalam kerusakan alat olahraga?</h6>
                </div>
                <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                    <input type="radio" class="btn-check" name="unsur" id="danger-outlined" autocomplete="off" value="Ya">
                    <label class="btn btn-outline-danger" for="danger-outlined">Ya</label>

                    <input type="radio" class="btn-check" name="unsur" id="primary-outlined" autocomplete="off" value="Tidak">
                    <label class="btn btn-outline-primary" for="primary-outlined">Tidak</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-12 mt-2">
                    <h6>Lampirkan Bukti</h6>
                </div>
                <div class="col-md-8 col-12 mt-2 mt-md-0 mb-3">
                    <input type="file" class="form-control" name="foto" accept=".jpg,.png,.jpeg">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-primary" id="addFormBtn">Tambah</button>
            <button type="submit" class="btn btn-success">Kirim</button>
        </div>
    </form>
</div>
<script>
    document.getElementById("addFormBtn").addEventListener("click", function() {
    let template = document.querySelector(".form-template").outerHTML;
    let container = document.createElement("div");
    container.innerHTML = template;

    // Mencari semua elemen input di dalam container
    let inputs = container.querySelectorAll("input");
    let uniqueTime = new Date().getTime();  // Dapatkan timestamp sebagai ID unik

    // Loop melalui setiap input
    inputs.forEach((input, index) => {
        if (input.id) {
            let newId = input.id + "-" + uniqueTime + "-" + index;
            let associatedLabel = container.querySelector("label[for='" + input.id + "']");
            
            // Modifikasi nama radio button agar unik untuk setiap form
            if (input.type === "radio") {
                input.name = input.name + "-" + uniqueTime;
            }

            input.id = newId;
            if (associatedLabel) {
                associatedLabel.setAttribute("for", newId);
            }
        }
    });

    document.querySelector("form").insertBefore(container, document.getElementById("addFormBtn").parentNode);
});

</script>
@endsection