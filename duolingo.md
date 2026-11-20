# Duolingo English Test (DET): Question Types, Timing, Weightage and Scoring — 2026

The Duolingo English Test assesses four English language skills: Reading, Writing, Listening and Speaking. Unlike IELTS, it does not use a fixed set of questions with a publicly defined mark allocation for every question type. The test adapts to the candidate's performance. [Test Center](https://blog.englishtest.duolingo.com/how-is-the-duolingo-english-test-scored/?utm_source=chatgpt.com)

For PerfectMocks, I recommend organizing Duolingo mock-test questions by **question type, response format, time limit, scoring method and assessed skill**.

The following guide reflects the published test format and official Duolingo research available as of October 2026. Individual timings and formats can change as the test evolves.

## 1. Reading question types

Reading tasks assess vocabulary recognition, contextual understanding, grammar, comprehension and the ability to identify main ideas.



| # | Question type | How it appears | Candidate's response | Preparation time | Response time |
|---:|---|---|---|---|---|
| 1 | Read and Complete (C-Test) | A paragraph contains words with their endings removed. The candidate must reconstruct the missing letters. | Type the missing letters to complete each word. | No separate preparation | Approximately 3 minutes |
| 2 | Read and Select | One word appears at a time. Some words are real English words; others are invented. | Select whether the word is a real English word. | None | Approximately 5 seconds per word |
| 3 | Interactive Reading — Complete the Sentences | A passage is presented with a missing sentence or phrase. | Select the best option to complete the sentence. | No separate preparation | Within the overall Interactive Reading task limit |
| 4 | Interactive Reading — Complete the Passage | A passage has a missing portion that must be completed using contextual clues. | Select the appropriate text to complete the passage. | No separate preparation | Within the overall Interactive Reading task limit |
| 5 | Interactive Reading — Highlight the Answer | The candidate reads a passage and answers a comprehension question by identifying supporting information. | Highlight the relevant words or sentence in the passage. | No separate preparation | Within the overall Interactive Reading task limit |
| 6 | Interactive Reading — Identify the Idea | A passage is followed by a question about its main idea or meaning. | Select the most appropriate answer. | No separate preparation | Within the overall Interactive Reading task limit |
| 7 | Interactive Reading — Title the Passage | The candidate reads a passage and identifies its most appropriate title. | Select the best title. | No separate preparation | Within the overall Interactive Reading task limit |
| 8 | Vocabulary in Context | A sentence contains a missing word. Context determines the appropriate word. | Type the missing word. | None | Task-specific timer |

**Important:** Interactive Reading is a connected, multi-step task. Its subtasks should not be treated as independent, full-length questions with separate timers. Duolingo's official research describes the passage expanding as the candidate progresses through the activities. [Test Center](https://blog.englishtest.duolingo.com/how-does-the-duolingo-english-test-measure-reading/)

### Example: Read and Complete

Suppose the question displays:

> Learning a new language requires consistent pract___ and patience.

The candidate enters:

`ice`

The completed word is `practice`.

**Suggested implementation for PerfectMocks:** Use a passage with multiple inline text inputs. Store the expected answer for each blank separately so that the system can evaluate each response.

---

## 2. Listening question types

Listening tasks assess word recognition, accurate transcription, comprehension and the ability to follow conversations.



| # | Question type | How it appears | Candidate's response | Preparation time | Response time |
|---:|---|---|---|---|---|
| 1 | Listen and Select | The candidate hears a word and determines whether it is a real English word. | Select the appropriate Yes/No response. | None | Approximately 5 seconds per word |
| 2 | Listen and Type (Dictation) | An audio recording plays a sentence. The candidate transcribes what they hear. | Type the complete sentence. | None | 60 seconds per sentence |
| 3 | Interactive Listening — Listen and Respond | The candidate participates in a simulated conversation by listening to prompts and selecting appropriate responses. | Select the most appropriate conversational response. | No separate preparation | Within the overall task limit |
| 4 | Interactive Listening — Listen and Complete | The candidate listens to a conversation and answers comprehension questions. | Select answers based on the conversation. | No separate preparation | Within the overall task limit |
| 5 | Interactive Listening — Summarize the Conversation | After completing the conversation, the candidate summarizes its main points. | Type a written summary. | No separate preparation | Task-specific timer |

The official Duolingo description confirms that Dictation allows up to two replays and gives the candidate 60 seconds to transcribe a sentence. Interactive Listening combines comprehension questions, conversational responses and a written summary. [Test Center](https://blog.englishtest.duolingo.com/how-duolingo-english-test-measures-listening/)

### Example: Listen and Type

The candidate hears:

> The university library closes at nine o'clock every evening.

The candidate must type the sentence as accurately as possible.

For PerfectMocks, you should consider storing:
- The audio file path.
- The expected transcript.
- Accepted transcription variants, where appropriate.
- The time limit.
- The number of permitted replays.

A strict character-for-character comparison may be inappropriate for some transcription tasks. Your scoring engine should account for acceptable variations where the task design permits them.

---

## 3. Writing question types

Writing tasks assess grammar, vocabulary, organization, relevance and the ability to develop ideas in written English.



| # | Question type | How it appears | Candidate's response | Preparation time | Response time |
|---:|---|---|---|---|---|
| 1 | Write About the Photo | An image appears on the screen. | Write a description of the image. | No separate preparation | Approximately 1 minute |
| 2 | Interactive Writing — Initial Response | A writing prompt asks the candidate to explain, narrate, evaluate or persuade. | Write a developed response. | No separate preparation | 5 minutes |
| 3 | Interactive Writing — Follow-up Response | A second prompt asks the candidate to elaborate on the original topic. | Extend or develop the initial response. | No separate preparation | 3 minutes |
| 4 | Writing Sample | The candidate receives an open-ended writing prompt. | Write an extended response. | No separate preparation | Approximately 5 minutes |

Interactive Writing consists of two connected stages: an initial response lasting five minutes, followed by a three-minute response to a related prompt. [Test Center](https://blog.englishtest.duolingo.com/does-the-det-measure-academic-english/)

### Example: Write About the Photo

Suppose the image shows students studying in a library.

The candidate might write:

> Several students are studying in a university library. Some are reading books, while others are working on their laptops. The environment appears quiet and suitable for academic study.

For PerfectMocks, this task needs an image, a timed text editor, a word counter and a scoring mechanism that evaluates more than spelling or grammar alone.

For open-ended writing tasks, consider evaluating:
- Relevance to the prompt.
- Development of ideas.
- Coherence and organization.
- Vocabulary.
- Grammar and sentence structure.

These are useful dimensions for a preparation platform, although they should not be presented as an exact reproduction of Duolingo's proprietary scoring algorithm.

---

## 4. Speaking question types

Speaking tasks assess pronunciation, fluency, grammar, vocabulary, coherence and the ability to communicate relevant ideas.



| # | Question type | How it appears | Candidate's response | Preparation time | Response time |
|---:|---|---|---|---|---|
| 1 | Speak About the Photo | A photograph appears on the screen. | Describe the image verbally. | No separate preparation | Approximately 90 seconds |
| 2 | Read, Then Speak | The candidate reads a written prompt. | Record a spoken response to the prompt. | No separate preparation | Approximately 90 seconds |
| 3 | Listen, Then Speak | The candidate listens to an audio prompt. | Record a spoken response. | No separate preparation | Task-specific timer |
| 4 | Interactive Speaking | A virtual character conducts a simulated conversation. Follow-up questions adapt to the candidate's responses. | Answer each conversational prompt verbally. | None | 35 seconds per response; typically 6–8 turns |
| 5 | Speaking Sample | The candidate receives an open-ended speaking prompt. | Record an extended spoken response. | No separate preparation | Approximately 3 minutes |

The official Interactive Speaking description specifies 6–8 conversational turns, 35 seconds per response and no preparation time. The follow-up prompts depend on the candidate's earlier responses. [Test Center](https://blog.englishtest.duolingo.com/interactive-speaking/)

**Implementation note:** Interactive Speaking is different from a traditional speaking question with a fixed prompt and one recording. A realistic implementation needs multiple turns, recording controls, prompt progression and response storage.

---

# 5. How much weight does each question carry?

This is the most important distinction for designing your scoring system.

**Duolingo does not publish a fixed public table assigning a specific number of marks or a percentage weight to every question type.**

Its official scoring explanation states that the test is computer-adaptive and uses Item Response Theory (IRT). The scoring process considers both the candidate's response and the difficulty of the questions presented. [Test Center](https://blog.englishtest.duolingo.com/how-is-the-duolingo-english-test-scored/)

Consequently, you should not assume that:
- Every question carries one mark.
- Every question type contributes equally to the final score.
- A candidate's percentage correct converts directly into a Duolingo score.
- A speaking response has the same numerical weight as a vocabulary question.

## How the official scoring system works

The DET reports scores on a scale of **10–160**, in increments of five.

It reports four individual subscores and four integrated subscores.

### Individual subscores

| Skill | What it measures |
|---|---|
| Reading | Understanding written English, vocabulary and reading comprehension |
| Writing | Written communication, grammar, vocabulary and development of ideas |
| Listening | Understanding spoken English and interpreting conversations |
| Speaking | Spoken communication, fluency, pronunciation and organization |

### Integrated subscores

| Integrated subscore | Calculation |
|---|---|
| Literacy | Average of Reading and Writing |
| Comprehension | Average of Reading and Listening |
| Conversation | Average of Listening and Speaking |
| Production | Average of Speaking and Writing |

The current published scoring model calculates each integrated subscore as the average of its two corresponding individual subscores. The overall score is the average of the four individual subscores, rounded to the nearest five-point increment. [Test Center](https://blog.englishtest.duolingo.com/det-subscores/?utm_source=chatgpt.com)

### Example calculation

Suppose a candidate receives these illustrative individual subscores:

| Individual skill | Score |
|---|---:|
| Reading | 120 |
| Writing | 110 |
| Listening | 125 |
| Speaking | 115 |

The integrated subscores would be:

| Integrated subscore | Calculation | Result |
|---|---|---:|
| Literacy | (120 + 110) / 2 | 115 |
| Comprehension | (120 + 125) / 2 | 122.5 |
| Conversation | (125 + 115) / 2 | 120 |
| Production | (115 + 110) / 2 | 112.5 |

The overall score is:

\[
\frac{120+110+125+115}{4}=117.5
\]

Rounded to the nearest five-point increment, the illustrative overall score is **120**.

These are hypothetical values to demonstrate the calculation, not a conversion formula from raw question marks.

---

# 6. How should PerfectMocks calculate marks?

Since you are building a mock-exam platform, I recommend separating **raw question scoring** from **estimated proficiency scoring**.

Trying to reproduce Duolingo's actual score from the number of correct answers alone would be misleading because the official scoring system is adaptive and accounts for item difficulty.

Instead, use the following model.

### A. Objective questions

For tasks such as vocabulary recognition and sentence completion, you can use:

| Evaluation | Example score |
|---|---:|
| Correct response | 1 |
| Incorrect response | 0 |
| Partially correct response | 0.5, if supported by the task's rubric |

These are suggested internal marks for your mock platform, not official Duolingo mark allocations.

### B. Writing and speaking questions

Use a rubric-based evaluation system.

For example:

| Evaluation criterion | Suggested internal weight |
|---|---:|
| Relevance and task completion | 25% |
| Grammar | 25% |
| Vocabulary | 20% |
| Coherence and organization | 20% |
| Fluency and pronunciation | 10% |
| **Total** | **100%** |

This is an example rubric for a preparation platform, not Duolingo's official scoring formula. You may need different criteria for writing and speaking.

### C. Interactive tasks

For Interactive Reading and Interactive Listening, evaluate each subtask independently, then aggregate the results according to your chosen mock-test rubric.

For example, an Interactive Reading passage might contain:
- One sentence-completion task.
- One passage-completion task.
- One highlight-the-answer task.
- One main-idea task.
- One title-selection task.

You could assign marks to each subtask and calculate a raw score for the entire passage.

However, that raw score should remain separate from the candidate's estimated DET proficiency score.

---

# 7. Recommended database structure for PerfectMocks

Given your existing Laravel application, I recommend keeping the question format flexible.

Your `question_options` table can support objective questions, but it should not be responsible for storing every kind of response. Writing and speaking tasks need different evaluation and response fields.

A useful question-type mapping would be:

| DET question type | Suggested internal `question_type` | Expected response |
|---|---|---|
| Read and Complete | `fill_in_blanks` | Text |
| Read and Select | `mcq_single` or custom word-classification type | Yes/No |
| Interactive Reading — Complete the Sentences | `mcq_single` | Option ID |
| Interactive Reading — Complete the Passage | `mcq_single` | Option ID |
| Interactive Reading — Highlight the Answer | `highlighting` | Selected text range |
| Interactive Reading — Identify the Idea | `mcq_single` | Option ID |
| Interactive Reading — Title the Passage | `mcq_single` | Option ID |
| Listen and Select | `mcq_single` or custom word-classification type | Yes/No |
| Listen and Type | `dictation` | Text |
| Interactive Listening — Listen and Respond | `mcq_single` | Option ID |
| Interactive Listening — Listen and Complete | `mcq_single` | Option ID |
| Interactive Listening — Summarize the Conversation | `writing` | Text |
| Write About the Photo | `writing` | Text |
| Interactive Writing | `interactive_writing` | Two connected text responses |
| Writing Sample | `essay` | Text |
| Speak About the Photo | `speaking` | Audio recording |
| Read, Then Speak | `speaking` | Audio recording |
| Listen, Then Speak | `speaking` | Audio recording |
| Interactive Speaking | `interactive_speaking` | Multiple audio responses |
| Speaking Sample | `speaking` | Audio recording |

**Important:** These are proposed internal mappings for your application, not official Duolingo database categories. Some task types need custom logic even when their response format resembles an existing question type.

For example, `mcq_single` can handle a multiple-choice subtask, but it does not by itself represent an entire Interactive Reading or Interactive Listening activity.

---

# 8. My recommendation

For PerfectMocks, I would implement the following architecture:

1. **Question type:** Identifies the interaction, such as `fill_in_blanks`, `dictation`, `writing` or `speaking`.
2. **Question configuration:** Stores the time limit, preparation time, media paths, replay allowance and response requirements.
3. **Answer storage:** Stores the candidate's actual response, including text, selected options or audio file references.
4. **Evaluation:** Calculates objective marks or rubric-based scores.
5. **Score reporting:** Displays raw performance and estimated proficiency separately.

For speaking and writing, retain the evaluation breakdown so students can see where they performed well and where they need improvement.

For the most accurate preparation experience, use Duolingo's official practice test to validate your task presentation and timings before deploying each question type.

### Official references

- [Official Duolingo English Test scoring explanation](https://blog.englishtest.duolingo.com/how-is-the-duolingo-english-test-scored/?utm_source=chatgpt.com)
- [Official reading task research](https://blog.englishtest.duolingo.com/how-does-the-duolingo-english-test-measure-reading/?utm_source=chatgpt.com)
- [Official listening task research](https://blog.englishtest.duolingo.com/how-duolingo-english-test-measures-listening/?utm_source=chatgpt.com)
- [Official Interactive Speaking description](https://blog.englishtest.duolingo.com/interactive-speaking/?utm_source=chatgpt.com)
- [Official Interactive Writing description](https://blog.englishtest.duolingo.com/does-the-det-measure-academic-english/?utm_source=chatgpt.com)