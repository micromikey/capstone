{{-- 
    EXAMPLE: How to add custom meta tags to this page
    
    Option 1: Add this at the top of your blade file (after <x-app-layout>)
--}}

@php
    $metaTitle = 'Emergency Readiness Assessment - HikeThere';
    $metaDescription = 'Review your hiking emergency preparedness checklist and ensure you have all essential safety items before hitting the trail. Stay safe with HikeThere.';
    $metaImage = asset('img/emergency-readiness-preview.jpg'); // Create this image
    $metaKeywords = 'emergency preparedness, hiking safety checklist, first aid kit, emergency supplies, hiking safety, outdoor safety';
@endphp

{{-- 
    Option 2: Use the component (recommended - cleaner code)
    Replace the meta section in app.blade.php with:
    
    <x-meta-tags 
        title="Emergency Readiness Assessment - HikeThere"
        description="Review your hiking emergency preparedness checklist and ensure you have all essential safety items."
        image="{{ asset('img/emergency-readiness-preview.jpg') }}"
        keywords="emergency preparedness, hiking safety checklist, first aid kit"
    />
--}}

{{--
    Option 3: Pass from controller (most flexible)
    
    In your EmergencyReadinessController:
    
    public function show($id)
    {
        $assessment = EmergencyReadiness::findOrFail($id);
        
        return view('hiker.emergency-readiness.show', [
            'assessment' => $assessment,
            'metaTitle' => 'Emergency Readiness: ' . $assessment->created_at->format('M d, Y') . ' - HikeThere',
            'metaDescription' => 'View your emergency preparedness assessment completed on ' . $assessment->created_at->format('F d, Y') . '. Score: ' . $assessment->score . '%',
            'metaImage' => asset('img/emergency-readiness.jpg'),
        ]);
    }
--}}
