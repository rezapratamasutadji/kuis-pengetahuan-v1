import './bootstrap';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

const rounds = {
    qualification: {
        key: 'qualification',
        label: 'Kualifikasi',
        questionCount: 25,
        answerLimit: 30,
    },
    semifinal: {
        key: 'semifinal',
        label: 'Semi Final',
        questionCount: 15,
        answerLimit: 25,
    },
    final: {
        key: 'final',
        label: 'Final',
        questionCount: 8,
        answerLimit: 15,
    },
};

const roundOrder = ['qualification', 'semifinal', 'final'];

const createRoundState = () => ({
    qualification: {},
    semifinal: {},
    final: {},
});

const getQuestionNumbers = (roundKey) =>
    Array.from({ length: rounds[roundKey].questionCount }, (_, index) => index + 1);

const getDifficultyClass = (roundKey, number) => {
    if (roundKey === 'qualification') {
        if (number <= 12) {
            return 'easy';
        }

        if (number <= 20) {
            return 'medium';
        }

        return 'hard';
    }

    if (roundKey === 'semifinal') {
        if (number <= 5) {
            return 'easy';
        }

        if (number <= 10) {
            return 'medium';
        }

        return 'hard';
    }

    return 'hard';
};

const getScoreByNumber = (roundKey, number) => {
    const difficulty = getDifficultyClass(roundKey, number);

    if (difficulty === 'easy') {
        return 10;
    }

    if (difficulty === 'medium') {
        return 15;
    }

    return 30;
};

const getAnsweredCount = (answeredQuestionsByRound, roundKey) =>
    Object.values(answeredQuestionsByRound[roundKey] ?? {}).reduce((total, numbers) => total + numbers.length, 0);

const getTopParticipants = (participants, scoreMap, limit) =>
    participants
        .map((participant, index) => ({
            participant,
            index,
            score: scoreMap[participant.id] ?? 0,
        }))
        .sort((left, right) => {
            if (right.score !== left.score) {
                return right.score - left.score;
            }

            return left.index - right.index;
        })
        .slice(0, limit)
        .map(({ participant }) => participant);

function App() {
    const [participants, setParticipants] = useState([]);
    const [activeRound, setActiveRound] = useState('qualification');
    const [selectedParticipant, setSelectedParticipant] = useState(null);
    const [participantScores, setParticipantScores] = useState(createRoundState);
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [selectedNumber, setSelectedNumber] = useState(null);
    const [question, setQuestion] = useState(null);
    const [selectedAnswer, setSelectedAnswer] = useState(null);
    const [answeredQuestions, setAnsweredQuestions] = useState(createRoundState);
    const [questionOwners, setQuestionOwners] = useState(createRoundState);
    const [loadingCategories, setLoadingCategories] = useState(true);
    const [loadingQuestion, setLoadingQuestion] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        const loadParticipants = async () => {
            try {
                const participantsResponse = await fetch('/api/quiz/participants');
                const participantsPayload = await participantsResponse.json();

                setParticipants(participantsPayload.data ?? []);
            } catch {
                setError('Data halaman gagal dimuat. Coba refresh halaman.');
            }
        };

        loadParticipants();
    }, []);

    useEffect(() => {
        const loadCategories = async () => {
            setLoadingCategories(true);

            try {
                const categoriesResponse = await fetch(`/api/quiz/categories?round=${activeRound}`);
                const categoriesPayload = await categoriesResponse.json();
                setCategories(categoriesPayload.data ?? []);
            } catch {
                setError('Data kategori gagal dimuat. Coba refresh halaman.');
            } finally {
                setLoadingCategories(false);
            }
        };

        loadCategories();
    }, [activeRound]);

    const qualificationAnsweredCount = getAnsweredCount(answeredQuestions, 'qualification');
    const semifinalAnsweredCount = getAnsweredCount(answeredQuestions, 'semifinal');
    const finalAnsweredCount = getAnsweredCount(answeredQuestions, 'final');

    const qualificationComplete = qualificationAnsweredCount >= rounds.qualification.answerLimit;
    const semifinalUnlocked = qualificationComplete;
    const semifinalComplete = semifinalAnsweredCount >= rounds.semifinal.answerLimit;
    const finalUnlocked = semifinalComplete;

    const roundParticipants = {
        qualification: participants.slice(0, 5),
        semifinal: getTopParticipants(participants, participantScores.qualification, 3),
        final: getTopParticipants(participants, participantScores.semifinal, 2),
    };

    const visibleParticipants = roundParticipants[activeRound] ?? [];
    const currentRoundScores = participantScores[activeRound] ?? {};
    const currentAnsweredMap = answeredQuestions[activeRound] ?? {};
    const currentQuestionOwners = questionOwners[activeRound] ?? {};
    const currentRoundConfig = rounds[activeRound];
    const currentAnsweredCount = getAnsweredCount(answeredQuestions, activeRound);
    const currentRoundComplete = currentAnsweredCount >= currentRoundConfig.answerLimit;
    const currentQuestionOwnerId = question && selectedCategory
        ? currentQuestionOwners[selectedCategory.slug]?.[question.number] ?? null
        : null;
    const currentQuestionOwner = visibleParticipants.find((participant) => participant.id === currentQuestionOwnerId) ?? null;
    const selectedAnswerIsCorrect = selectedAnswer && question ? selectedAnswer === question.correct_option : false;
    const canAssignParticipant = Boolean(question && selectedAnswerIsCorrect && !currentQuestionOwnerId);
    const isQuestionModalOpen = Boolean(selectedCategory && selectedNumber);
    const mustAssignParticipant = Boolean(selectedAnswerIsCorrect && selectedAnswer && !currentQuestionOwnerId);

    const handleSelectRound = (roundKey) => {
        if (roundKey === 'semifinal' && !semifinalUnlocked) {
            setError('Semi Final akan terbuka setelah 30 soal di babak Kualifikasi terjawab.');
            return;
        }

        if (roundKey === 'final' && !finalUnlocked) {
            setError('Final akan terbuka setelah 25 soal di babak Semi Final terjawab.');
            return;
        }

        setActiveRound(roundKey);
        setSelectedParticipant(null);
        setSelectedCategory(null);
        setSelectedNumber(null);
        setQuestion(null);
        setSelectedAnswer(null);
        setError('');
    };

    const handleSelectCategory = (category) => {
        if (currentRoundComplete) {
            setError(`Babak ${currentRoundConfig.label} sudah mencapai batas ${currentRoundConfig.answerLimit} soal terjawab.`);
            return;
        }

        setSelectedCategory(category);
        setSelectedParticipant(null);
        setSelectedNumber(null);
        setQuestion(null);
        setSelectedAnswer(null);
        setError('');
    };

    const handleSelectNumber = async (number) => {
        if (!selectedCategory || currentRoundComplete) {
            return;
        }

        setLoadingQuestion(true);
        setSelectedParticipant(null);
        setSelectedNumber(number);
        setQuestion(null);
        setSelectedAnswer(null);
        setError('');

        try {
            const response = await fetch(`/api/quiz/categories/${selectedCategory.slug}/questions/${number}?round=${activeRound}`);

            if (!response.ok) {
                throw new Error('Soal tidak ditemukan');
            }

            const payload = await response.json();
            setQuestion(payload.data);
        } catch {
            setError('Soal untuk nomor ini belum tersedia atau gagal dimuat.');
        } finally {
            setLoadingQuestion(false);
        }
    };

    const handleResetQuiz = () => {
        setActiveRound('qualification');
        setSelectedParticipant(null);
        setParticipantScores(createRoundState());
        setAnsweredQuestions(createRoundState());
        setQuestionOwners(createRoundState());
        setSelectedCategory(null);
        setSelectedNumber(null);
        setQuestion(null);
        setSelectedAnswer(null);
        setError('');
    };

    const handleCloseQuestionModal = () => {
        if (mustAssignParticipant) {
            return;
        }

        setSelectedParticipant(null);
        setSelectedNumber(null);
        setQuestion(null);
        setSelectedAnswer(null);
        setError('');
    };

    const handleSelectAnswer = (answerKey) => {
        if (!question || !selectedCategory || selectedAnswer) {
            return;
        }

        setSelectedParticipant(null);
        setSelectedAnswer(answerKey);
        setAnsweredQuestions((current) => {
            const roundAnswered = current[activeRound] ?? {};
            const answeredNumbers = roundAnswered[selectedCategory.slug] ?? [];

            if (answeredNumbers.includes(question.number)) {
                return current;
            }

            return {
                ...current,
                [activeRound]: {
                    ...roundAnswered,
                    [selectedCategory.slug]: [...answeredNumbers, question.number],
                },
            };
        });
    };

    const handleAssignParticipant = (participant) => {
        if (!question || !selectedCategory || !selectedAnswer || currentQuestionOwnerId) {
            return;
        }

        setSelectedParticipant(participant);
        setQuestionOwners((current) => {
            const roundOwners = current[activeRound] ?? {};
            const categoryOwners = roundOwners[selectedCategory.slug] ?? {};

            return {
                ...current,
                [activeRound]: {
                    ...roundOwners,
                    [selectedCategory.slug]: {
                        ...categoryOwners,
                        [question.number]: participant.id,
                    },
                },
            };
        });

        if (selectedAnswerIsCorrect) {
            setParticipantScores((current) => ({
                ...current,
                [activeRound]: {
                    ...current[activeRound],
                    [participant.id]: (current[activeRound]?.[participant.id] ?? 0) + getScoreByNumber(activeRound, question.number),
                },
            }));
        }
    };

    return (
        <div className="quiz-shell">
            <div className="ambient ambient-one" />
            <div className="ambient ambient-two" />

            <main className="quiz-app">
                <section className="round-panel panel">
                    <div className="section-head round-head">
                        <div>
                            <h2>Pilih Babak</h2>
                        </div>
                        <span className="pill">
                            {currentAnsweredCount}
                            {' / '}
                            {currentRoundConfig.answerLimit}
                            {' '}
                            soal terjawab
                        </span>
                    </div>

                    <div className="round-grid">
                        {roundOrder.map((roundKey) => {
                            const roundConfig = rounds[roundKey];
                            const isLocked = (roundKey === 'semifinal' && !semifinalUnlocked) || (roundKey === 'final' && !finalUnlocked);
                            const answeredCount = getAnsweredCount(answeredQuestions, roundKey);
                            const roundCompleted = answeredCount >= roundConfig.answerLimit;

                            return (
                                <button
                                    type="button"
                                    key={roundKey}
                                    className={`round-card ${activeRound === roundKey ? 'active' : ''} ${isLocked ? 'locked' : ''}`}
                                    onClick={() => handleSelectRound(roundKey)}
                                >
                                    <div>
                                        <p className="participant-label">Babak</p>
                                        <h3>{roundConfig.label}</h3>
                                    </div>
                                    <strong>
                                        {answeredCount}
                                        {' / '}
                                        {roundConfig.answerLimit}
                                    </strong>
                                    <span className="round-card-meta">
                                        {roundCompleted
                                            ? 'Selesai'
                                            : isLocked
                                                ? 'Terkunci'
                                                : `${roundConfig.questionCount} soal per kategori`}
                                    </span>
                                </button>
                            );
                        })}
                    </div>
                </section>

                <section className="content-grid">
                    <div className="panel categories-panel">
                        <div className="section-head">
                            <div>
                                <h2>Pilih Kategori</h2>
                            </div>
                            <span className="pill">{categories.length || 10} kategori</span>
                        </div>

                        {loadingCategories ? (
                            <p className="state-text">Memuat kategori...</p>
                        ) : (
                            <div className="category-grid">
                                {categories.map((category) => {
                                    const isDisabled = currentRoundComplete;

                                    return (
                                        <button
                                            type="button"
                                            key={category.slug}
                                            disabled={isDisabled}
                                            className={`category-card ${selectedCategory?.slug === category.slug ? 'active' : ''} ${isDisabled ? 'locked' : ''}`}
                                            onClick={() => handleSelectCategory(category)}
                                        >
                                            <h3>{category.name}</h3>
                                        </button>
                                    );
                                })}
                            </div>
                        )}

                        <div className="participant-section participant-summary-section">
                            <div className="section-head participant-section-head">
                                <div>
                                    <h2>Skor Peserta</h2>
                                </div>
                                <span className="pill">{visibleParticipants.length} peserta</span>
                            </div>

                            <div className="participant-grid participant-summary-grid">
                                {visibleParticipants.map((participant) => (
                                    <div key={participant.id} className="participant-card participant-card-static">
                                        <div>
                                            <p className="participant-label">Peserta</p>
                                            <h3>{participant.name}</h3>
                                        </div>
                                        <strong>{currentRoundScores[participant.id] ?? 0} poin</strong>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="panel question-panel">
                        <div className="section-head">
                            <div>
                                <h2>Pilih Nomor Soal</h2>
                            </div>
                            <div className="question-actions">
                                <button type="button" className="reset-button" onClick={handleResetQuiz}>
                                    Segarkan
                                </button>
                                <span className="pill">{selectedCategory?.name ?? currentRoundConfig.label}</span>
                            </div>
                        </div>

                        {!selectedCategory ? (
                            <p className="state-text">
                                Pilih salah satu kategori untuk menampilkan nomor soal 1 sampai {currentRoundConfig.questionCount}.
                            </p>
                        ) : (
                            <>
                                <div className="difficulty-legend">
                                    <div className="legend-item">
                                        <span className="legend-swatch easy" />
                                        <p>Easy 10 Poin</p>
                                    </div>
                                    <div className="legend-item">
                                        <span className="legend-swatch medium" />
                                        <p>Medium 15 Poin</p>
                                    </div>
                                    <div className="legend-item">
                                        <span className="legend-swatch hard" />
                                        <p>Hard 30 Poin</p>
                                    </div>
                                </div>

                                <div className="round-status-card">
                                    <strong>
                                        {currentAnsweredCount}
                                        {' / '}
                                        {currentRoundConfig.answerLimit}
                                        {' '}
                                        soal terjawab di babak {currentRoundConfig.label}
                                    </strong>
                                    <p>
                                        {currentRoundComplete
                                            ? `Babak ${currentRoundConfig.label} sudah selesai. Soal dan kategori yang tersisa sekarang terkunci.`
                                            : `Babak ${currentRoundConfig.label} masih aktif. Sisa ${currentRoundConfig.answerLimit - currentAnsweredCount} soal lagi sebelum babak ditutup.`}
                                    </p>
                                </div>

                                <div className="number-grid">
                                    {getQuestionNumbers(activeRound).map((number) => {
                                        const isAvailable = selectedCategory.available_numbers.includes(number);
                                        const isAnswered = (currentAnsweredMap[selectedCategory.slug] ?? []).includes(number);
                                        const difficultyClass = getDifficultyClass(activeRound, number);

                                        return (
                                            <button
                                                type="button"
                                                key={number}
                                                disabled={!isAvailable || isAnswered || currentRoundComplete}
                                                className={`number-card ${difficultyClass} ${selectedNumber === number ? 'active' : ''} ${isAnswered ? 'answered' : ''}`}
                                                onClick={() => handleSelectNumber(number)}
                                            >
                                                {number}
                                            </button>
                                        );
                                    })}
                                </div>
                            </>
                        )}
                    </div>
                </section>
            </main>

            {isQuestionModalOpen && (
                <div className="modal-overlay" onClick={mustAssignParticipant ? undefined : handleCloseQuestionModal}>
                    <div className="modal-card" onClick={(event) => event.stopPropagation()}>
                        <div className="modal-head">
                            <div>
                                <p className="participant-label">Soal Aktif</p>
                                <h2>{selectedCategory?.name}</h2>
                            </div>
                            <button
                                type="button"
                                className="modal-close"
                                onClick={handleCloseQuestionModal}
                                disabled={mustAssignParticipant}
                            >
                                Tutup
                            </button>
                        </div>

                        {loadingQuestion && <p className="state-text">Memuat pertanyaan nomor {selectedNumber}...</p>}
                        {error && <p className="state-text state-error">{error}</p>}

                        {question && (
                            <>
                                <div className="question-card modal-question-card">
                                    <div className="question-meta">
                                        <span>{question.category.name}</span>
                                        <strong>
                                            Nomor {question.number}
                                            {' - '}
                                            {getDifficultyClass(activeRound, question.number).toUpperCase()}
                                        </strong>
                                    </div>

                                    <h3>{question.prompt}</h3>

                                    <div className="options-grid">
                                        {Object.entries(question.options).map(([key, label]) => {
                                            const isSelected = selectedAnswer === key;
                                            const showResult = Boolean(selectedAnswer);
                                            const isRightAnswer = question.correct_option === key;

                                            return (
                                                <button
                                                    type="button"
                                                    key={key}
                                                    className={[
                                                        'option-card',
                                                        isSelected ? 'selected' : '',
                                                        showResult && isRightAnswer ? 'correct' : '',
                                                        showResult && isSelected && !isRightAnswer ? 'wrong' : '',
                                                    ].join(' ')}
                                                    onClick={() => handleSelectAnswer(key)}
                                                    disabled={showResult}
                                                >
                                                    <span>{key.toUpperCase()}</span>
                                                    <p>{label}</p>
                                                </button>
                                            );
                                        })}
                                    </div>

                                </div>

                                <div className="participant-section modal-participant-section">
                                    <div className="section-head participant-section-head">
                                        <div>
                                            <h2>Pilih Peserta</h2>
                                        </div>
                                        <span className="pill">
                                            {currentQuestionOwner ? currentQuestionOwner.name : 'Belum dipilih'}
                                        </span>
                                    </div>

                                    <div className="participant-grid participant-grid-vertical">
                                        {visibleParticipants.map((participant) => (
                                            <button
                                                type="button"
                                                key={participant.id}
                                                disabled={!canAssignParticipant}
                                                className={`participant-card ${selectedParticipant?.id === participant.id ? 'active' : ''} ${!canAssignParticipant ? 'disabled' : ''}`}
                                                onClick={() => handleAssignParticipant(participant)}
                                            >
                                                <div>
                                                    <p className="participant-label">Peserta</p>
                                                    <h3>{participant.name}</h3>
                                                </div>
                                                <strong>{currentRoundScores[participant.id] ?? 0} poin</strong>
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

const rootElement = document.getElementById('app');

if (rootElement) {
    createRoot(rootElement).render(<App />);
}
