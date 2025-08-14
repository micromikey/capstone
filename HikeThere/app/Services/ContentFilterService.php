<?php

namespace App\Services;

use Illuminate\Support\Str;

class ContentFilterService
{
    /**
     * List of explicit/inappropriate words and phrases
     */
    private array $explicitWords = [
        // Profanity and explicit content
        'fuck', 'shit', 'bitch', 'asshole', 'dick', 'pussy', 'cock', 'cunt',
        'bastard', 'whore', 'slut', 'motherfucker', 'fucker', 'fucking',
        
        // Violence-related
        'kill', 'murder', 'suicide', 'bomb', 'terrorist', 'hate', 'racist',
        
        // Drugs and illegal activities
        'drugs', 'cocaine', 'heroin', 'marijuana', 'weed', 'illegal',
        
        // Sexual content
        'sex', 'sexual', 'nude', 'naked', 'porn', 'pornography', 'rape', 'raping',
        
        // Hate speech
        'nazi', 'hitler', 'racist', 'discrimination', 'hate speech',
    ];

    /**
     * List of trail-related keywords to check relevance
     */
    private array $trailKeywords = [
        'hiking', 'trail', 'mountain', 'nature', 'outdoor', 'adventure',
        'camping', 'forest', 'peak', 'summit', 'climb', 'trek',
        'scenery', 'view', 'landscape', 'wildlife', 'plants', 'trees',
        'waterfall', 'river', 'lake', 'stream', 'path', 'route',
        'difficulty', 'elevation', 'distance', 'duration', 'weather',
        'equipment', 'gear', 'boots', 'backpack', 'tent', 'map',
        'safety', 'first aid', 'emergency', 'rescue', 'guide',
        'experience', 'challenge', 'achievement', 'memories', 'friends',
        'family', 'group', 'solo', 'guided', 'tour', 'package',
        'booking', 'reservation', 'cost', 'price', 'fee', 'payment',
        'transportation', 'accommodation', 'food', 'water', 'supplies',
        'photography', 'camera', 'pictures', 'videos', 'documentation',
        'conservation', 'environment', 'sustainability', 'leave no trace',
        'wilderness', 'backcountry', 'remote', 'isolated', 'peaceful',
        'quiet', 'serene', 'beautiful', 'amazing', 'incredible', 'wonderful',
        'exhilarating', 'challenging', 'rewarding', 'fulfilling', 'satisfying',
        'memorable', 'unforgettable', 'life-changing', 'transformative',
        'spiritual', 'meditative', 'reflective', 'contemplative', 'mindful',
        'physical', 'exercise', 'fitness', 'health', 'wellness', 'mental health',
        'stress relief', 'relaxation', 'recreation', 'leisure', 'hobby',
        'passion', 'interest', 'enthusiasm', 'excitement', 'anticipation',
        'preparation', 'planning', 'research', 'information', 'knowledge',
        'skills', 'experience', 'expertise', 'proficiency', 'competence',
        'confidence', 'courage', 'determination', 'perseverance', 'resilience',
        'teamwork', 'cooperation', 'support', 'encouragement', 'motivation',
        'inspiration', 'role model', 'mentor', 'leader', 'guide', 'teacher',
        'learning', 'education', 'training', 'workshop', 'seminar', 'class',
        'certification', 'qualification', 'accreditation', 'recognition',
        'achievement', 'accomplishment', 'success', 'victory', 'triumph',
        'celebration', 'commemoration', 'remembrance', 'honor', 'respect',
        'gratitude', 'appreciation', 'thankfulness', 'blessing', 'gift',
        'opportunity', 'privilege', 'advantage', 'benefit', 'value',
        'meaning', 'purpose', 'significance', 'importance', 'relevance',
        'connection', 'relationship', 'bond', 'friendship', 'camaraderie',
        'community', 'society', 'culture', 'heritage', 'tradition', 'custom',
        'history', 'past', 'present', 'future', 'legacy', 'impact',
        'influence', 'contribution', 'participation', 'involvement', 'engagement',
        'commitment', 'dedication', 'devotion', 'loyalty', 'faithfulness',
        'reliability', 'dependability', 'trustworthiness', 'honesty', 'integrity',
        'ethics', 'morals', 'values', 'principles', 'standards', 'guidelines',
        'rules', 'regulations', 'policies', 'procedures', 'protocols',
        'safety', 'security', 'protection', 'prevention', 'avoidance',
        'risk', 'danger', 'hazard', 'threat', 'challenge', 'obstacle',
        'difficulty', 'problem', 'issue', 'concern', 'worry', 'anxiety',
        'fear', 'nervousness', 'tension', 'pressure', 'stress', 'strain',
        'fatigue', 'exhaustion', 'tiredness', 'weakness', 'soreness', 'pain',
        'injury', 'illness', 'sickness', 'disease', 'condition', 'symptom',
        'treatment', 'medicine', 'medication', 'therapy', 'rehabilitation',
        'recovery', 'healing', 'improvement', 'progress', 'development',
        'growth', 'advancement', 'enhancement', 'upgrade', 'improvement',
        'betterment', 'refinement', 'polish', 'perfection', 'excellence',
        'quality', 'standard', 'level', 'grade', 'rank', 'position',
        'status', 'reputation', 'image', 'appearance', 'look', 'style',
        'fashion', 'trend', 'popular', 'famous', 'well-known', 'recognized',
        'acknowledged', 'accepted', 'approved', 'endorsed', 'recommended',
        'suggested', 'proposed', 'offered', 'provided', 'supplied', 'given',
        'shared', 'distributed', 'spread', 'circulated', 'disseminated',
        'communicated', 'conveyed', 'expressed', 'stated', 'declared',
        'announced', 'proclaimed', 'publicized', 'advertised', 'promoted',
        'marketed', 'sold', 'bought', 'purchased', 'acquired', 'obtained',
        'gained', 'earned', 'won', 'achieved', 'accomplished', 'completed',
        'finished', 'ended', 'concluded', 'terminated', 'stopped', 'halted',
        'paused', 'suspended', 'interrupted', 'disrupted', 'disturbed',
        'bothered', 'annoyed', 'irritated', 'frustrated', 'angry', 'mad',
        'upset', 'disappointed', 'sad', 'depressed', 'miserable', 'unhappy',
        'dissatisfied', 'displeased', 'discontent', 'uncomfortable', 'uneasy',
        'nervous', 'anxious', 'worried', 'concerned', 'troubled', 'distressed',
        'agitated', 'excited', 'thrilled', 'delighted', 'pleased', 'happy',
        'joyful', 'cheerful', 'glad', 'satisfied', 'content', 'pleased',
        'grateful', 'thankful', 'appreciative', 'blessed', 'fortunate',
        'lucky', 'privileged', 'advantaged', 'benefited', 'valued',
        'cherished', 'treasured', 'prized', 'valued', 'esteemed', 'respected',
        'admired', 'appreciated', 'loved', 'cared for', 'supported',
        'encouraged', 'motivated', 'inspired', 'influenced', 'affected',
        'changed', 'transformed', 'modified', 'altered', 'adjusted',
        'adapted', 'accommodated', 'fitted', 'suited', 'matched', 'compatible',
        'suitable', 'appropriate', 'proper', 'correct', 'right', 'good',
        'excellent', 'outstanding', 'superior', 'exceptional', 'extraordinary',
        'remarkable', 'notable', 'significant', 'important', 'essential',
        'necessary', 'required', 'needed', 'wanted', 'desired', 'preferred',
        'chosen', 'selected', 'picked', 'elected', 'voted', 'decided',
        'determined', 'resolved', 'settled', 'agreed', 'consented', 'approved',
        'authorized', 'permitted', 'allowed', 'enabled', 'empowered', 'capable',
        'able', 'competent', 'qualified', 'skilled', 'talented', 'gifted',
        'intelligent', 'smart', 'clever', 'wise', 'knowledgeable', 'educated',
        'informed', 'aware', 'conscious', 'mindful', 'attentive', 'careful',
        'cautious', 'prudent', 'sensible', 'reasonable', 'rational', 'logical',
        'sensible', 'practical', 'realistic', 'achievable', 'attainable',
        'reachable', 'accessible', 'available', 'obtainable', 'acquirable',
        'gettable', 'procurable', 'securable', 'attainable', 'achievable',
        'accomplishable', 'doable', 'feasible', 'possible', 'practical',
        'realistic', 'reasonable', 'sensible', 'logical', 'rational',
        'intelligent', 'smart', 'clever', 'wise', 'knowledgeable', 'educated',
        'informed', 'aware', 'conscious', 'mindful', 'attentive', 'careful',
        'cautious', 'prudent', 'sensible', 'reasonable', 'rational', 'logical',
        'sensible', 'practical', 'realistic', 'achievable', 'attainable',
        'reachable', 'accessible', 'available', 'obtainable', 'acquirable',
        'gettable', 'procurable', 'securable', 'attainable', 'achievable',
        'accomplishable', 'doable', 'feasible', 'possible', 'practical',
        'realistic', 'reasonable', 'sensible', 'logical', 'rational',
    ];

    /**
     * Check if content contains explicit/inappropriate content
     */
    public function containsExplicitContent(string $content): bool
    {
        $content = Str::lower($content);
        
        foreach ($this->explicitWords as $word) {
            if (Str::contains($content, $word)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if content is relevant to hiking/trails
     */
    public function isTrailRelevant(string $content): bool
    {
        $content = Str::lower($content);
        $relevantCount = 0;
        $totalKeywords = count($this->trailKeywords);
        
        foreach ($this->trailKeywords as $keyword) {
            if (Str::contains($content, $keyword)) {
                $relevantCount++;
            }
        }
        
        // Content is considered relevant if it contains at least 20% trail-related keywords
        $relevancePercentage = ($relevantCount / $totalKeywords) * 100;
        return $relevancePercentage >= 20;
    }

    /**
     * Get content moderation score (0-100, higher = more appropriate)
     */
    public function getModerationScore(string $content): int
    {
        $score = 100;
        
        // Deduct points for explicit content
        if ($this->containsExplicitContent($content)) {
            $score -= 50;
        }
        
        // Deduct points for low relevance
        if (!$this->isTrailRelevant($content)) {
            $score -= 30;
        }
        
        // Deduct points for excessive length (spam detection)
        if (Str::length($content) > 1000) {
            $score -= 20;
        }
        
        // Deduct points for repetitive content
        if ($this->hasRepetitiveContent($content)) {
            $score -= 25;
        }
        
        return max(0, $score);
    }

    /**
     * Check if content is approved for posting
     */
    public function isContentApproved(string $content): bool
    {
        $score = $this->getModerationScore($content);
        return $score >= 70; // Content needs 70+ score to be approved
    }

    /**
     * Get moderation feedback for content
     */
    public function getModerationFeedback(string $content): array
    {
        $feedback = [];
        $score = $this->getModerationScore($content);
        
        if ($this->containsExplicitContent($content)) {
            $feedback[] = 'Content contains inappropriate language or explicit content.';
        }
        
        if (!$this->isTrailRelevant($content)) {
            $feedback[] = 'Content may not be relevant to hiking or trail experiences.';
        }
        
        if (Str::length($content) > 1000) {
            $feedback[] = 'Content is very long. Consider being more concise.';
        }
        
        if ($this->hasRepetitiveContent($content)) {
            $feedback[] = 'Content appears to be repetitive or spam-like.';
        }
        
        if ($score < 70) {
            $feedback[] = 'Content does not meet community guidelines.';
        }
        
        return [
            'score' => $score,
            'approved' => $this->isContentApproved($content),
            'feedback' => $feedback,
            'explicit_content' => $this->containsExplicitContent($content),
            'trail_relevant' => $this->isTrailRelevant($content)
        ];
    }

    /**
     * Check for repetitive content (spam detection)
     */
    private function hasRepetitiveContent(string $content): bool
    {
        $words = Str::of($content)->lower()->split('/\s+/');
        $wordCounts = array_count_values($words->toArray());
        
        foreach ($wordCounts as $word => $count) {
            if (Str::length($word) > 3 && $count > 5) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Sanitize content by removing explicit words
     */
    public function sanitizeContent(string $content): string
    {
        $sanitized = $content;
        
        foreach ($this->explicitWords as $word) {
            $sanitized = Str::replace($word, str_repeat('*', Str::length($word)), $sanitized);
            $sanitized = Str::replace(Str::title($word), str_repeat('*', Str::length($word)), $sanitized);
            $sanitized = Str::replace(Str::upper($word), str_repeat('*', Str::length($word)), $sanitized);
        }
        
        return $sanitized;
    }
}
