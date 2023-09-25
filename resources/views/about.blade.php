@extends('layouts.app')

@section('content')
    <div class="w-3/4 mx-auto text-center">


        {{-- https://sibcloud-my.sharepoint.com/:w:/g/personal/claire_clivaz_sib_swiss/EfhuSVo_ueFJl92yrOqjnxEB26mQ_oNa1INg5bK9ue92xw?e=jKHBoQ --}}



        <h1>About the Codex Bobbiensis Virtual Research Environment (VRE) </h1>



        <p class="text-justify">The VRE Codex Bobbiensis (<a
                href="https://bobbiensis.sib.swiss">https://bobbiensis.sib.swiss</a>) has
            resulted from a fruitful collaboration between
            the <i>Biblioteca Nazionale Universitaria di Torino</i> (Italy) and Claire Clivaz, Head of Digital Humanities +
            (SIB,
            Lausanne, CH).</p>

        <br><br>

        <p class="text-justify">This venerable manuscript has transmitted one of the most ancient Latin exemplars of the
            Gospels of Mark 8:18-16:8
            and Matthew 1:1-14:161. As part of the SNSF project MARK16 (grant n°179755), four folios of the Gospel of Mark
            have
            been for the first time published: <a
                href="https://mr-mark16.sib.swiss/manuscript/VL1">https://mr-mark16.sib.swiss/manuscript/VL1</a></p>

        <br><br>


        <p class="text-justify">
            Guglielmo Bartoletti, Director of the <i>Biblioteca Nazionale Universitaria di Torino</i> has then graciously
            transmitted
            digital images of the manuscript to our team for making them available visible on a VRE. We have taken the
            opportunity of some IT-development time left at the end of the SNSF MARK16 project to start building this VRE,
            that
            includes 192 digital images. Researchers are now able to study the remaining folios in free access online. We
            provide a first folio with <a href="{{ route('manuscript.show', \App\Models\Manuscript::find(1)->name ) }}">our editorial model </a>(f. 41r): a Latin transcription with highlighted corresponding
            lines
            on the folios, and editorial notes.
            A pdf and an xml version of f. 41r can be downloaded from
            our
            VRE, and on the public open repository <a href="https://doi.org/10.34847/nkl.82341244" target="_blank">Nakala</a>
            (<a href="https://www.huma-num.fr" target="_blank">Huma-Num</a>, <a
                href="https://www.cnrs.fr" target="_blank">CNRS</a>). The annotated transcription of the
            next folios
            is
            open to collaboration: if you are interested in transcribing and annotating a dataset, please contact
            claire.clivaz@sib.swiss.
        </p>


        <br><br>

        <p class="text-justify">
            Readers will find a concise description of the manuscript, as well as a list of its content and an indicative
            bibliography under the tab <a href="{{ route("vl1") }}">“VL 1”</a>. I can only express my warmest gratitude to Guglielmo Bartoletti and to
            Fabio
            Uliana, librarian, for their constant support. My gratitude goes also to Silvano Aldà, IT developer, and to
            Elisa Nury, DH+ scientific researcher, and our IT-SIB colleagues for their support. Finally, I warmly thank
            Elena
            Giglia (OPERAS) who put me in touch with the Library of Turin. When New Testament studies interact with Digital
            Humanities, it becomes possible to bring to life such a venerable manuscript. But nothing, of course, will
            replace a
            personal visit to the Codex Bobbiensis in Turin. As told by Luke once, it matters to “have become eyewitnesses,”
            <span class="font-gentiumPlus">αὐτόπται […] γενόμενοι</span> (Luke 1:2).
        </p>


        <br><br>

        <p class="text-right"> Claire Clivaz, Lausanne, September 2023</p>






    </div>
@endsection
