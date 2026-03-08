# 👨‍🏫 FITUR GURU (PEMBIMBING)

**Role:** Guru pembimbing yang mengawasi dan membimbing siswa operator

---

## 🎯 Akses Menu Guru:

### 1. Dashboard Guru
**Path:** `/guru/dashboard`

**Fitur:**
- Overview kelas/siswa
- Total siswa operator
- Active orders
- Completed orders this month
- Class performance metrics
- Student rankings
- Revenue generated
- Quick actions

**Metrics:**
- Total Students
- Active Operators
- Orders This Month
- Completion Rate
- Average Rating
- Total Earnings (Class)
- Top Performers
- Students Need Help

**Use Case:**
- Monitor class performance
- Identify top performers
- Identify students need help
- Track progress
- Quick intervention

---

### 2. Student Management
**Path:** `/guru/students`

**Fitur:**

#### A. Student List
**Information per Student:**
- Name & student ID
- Operator level (Junior/Operator/Senior/Expert)
- Total orders completed
- Active orders
- Average rating
- Total earnings
- Last active
- Status (Active/Inactive)

**Actions:**
- View student profile
- View student orders
- View student performance
- Send message
- Assign training
- Give feedback

#### B. Student Performance
**Per Student Metrics:**
- Orders completed
- Success rate
- Average rating
- Response time
- Revision rate
- Client satisfaction
- Earnings
- Skill assessment

**Performance Indicators:**
- 🟢 Excellent (Rating 4.8+, High completion)
- 🟡 Good (Rating 4.0-4.7, Normal completion)
- 🟠 Need Improvement (Rating 3.5-3.9, Some issues)
- 🔴 Need Attention (Rating < 3.5, Multiple issues)

#### C. Student Grouping
**Groups:**
- Top Performers (untuk challenge lebih)
- Average Performers (untuk motivasi)
- Need Help (untuk intensive mentoring)
- Inactive (untuk follow-up)

**Use Case:**
- Personalized mentoring
- Targeted intervention
- Motivation & recognition
- Skill development

---

### 3. Order Monitoring
**Path:** `/guru/orders`

**Fitur:**

#### A. All Orders Overview
**View:**
- All orders from students
- Filter by:
  - Student name
  - Status
  - Category
  - Date range
  - Client
- Sort by:
  - Deadline
  - Status
  - Student
  - Budget

**Order Information:**
- Student name
- Client name
- Category
- Brief
- Deadline
- Status
- Budget
- Progress

#### B. Order Status Monitoring
**Status Tracking:**
1. **Pending** - Order available, not taken
2. **Accepted** - Student accepted order
3. **In Progress** - Student working
4. **Submitted** - Waiting client review
5. **Revision** - Client request revision
6. **Completed** - Order done
7. **Cancelled** - Order cancelled

**Alerts:**
- 🔴 Deadline approaching (< 24 hours)
- 🟠 Revision requested
- 🟡 Long response time
- 🟢 Completed on time

#### C. Quality Control
**Review Features:**
- View student work
- Check quality
- Provide feedback
- Suggest improvements
- Approve before submit (optional)

**Quality Checklist:**
- Grammar & spelling
- Tone appropriate
- Brief requirements met
- Platform-optimized
- Creative & engaging
- Professional quality

**Use Case:**
- Ensure quality
- Prevent issues
- Guide students
- Maintain reputation

---

### 4. Training & Development
**Path:** `/guru/training`

**Fitur:**

#### A. Training Materials
**Create & Manage:**
- Copywriting tutorials
- Video lessons
- Templates & examples
- Case studies
- Best practices
- Industry insights

**Categories:**
- Basics of copywriting
- Platform-specific tips
- Client communication
- Time management
- Quality assurance
- Advanced techniques

#### B. Assignments
**Create Assignments:**
- Practice exercises
- Real-world scenarios
- Skill assessments
- Group projects
- Portfolio building

**Track Progress:**
- Submission status
- Grades/scores
- Feedback given
- Improvement tracking

#### C. Certification Program
**Manage Certifications:**
- Create courses
- Set requirements
- Grade assessments
- Issue certificates
- Track completion

**Certification Levels:**
- Basic Copywriting
- Platform Specialist
- Category Expert
- Professional Copywriter

**Use Case:**
- Skill development
- Structured learning
- Quality improvement
- Motivation

---

### 5. Performance Analytics
**Path:** `/guru/analytics`

**Fitur:**

#### A. Class Performance
**Metrics:**
- Total orders completed
- Average completion rate
- Average rating
- Total revenue generated
- Growth trends
- Category distribution
- Platform distribution

**Visualizations:**
- Performance over time
- Student comparison
- Category breakdown
- Revenue trends
- Rating distribution

#### B. Individual Analytics
**Per Student:**
- Performance trends
- Strengths & weaknesses
- Improvement areas
- Skill progression
- Earnings growth

#### C. Comparative Analysis
**Compare:**
- Student vs student
- Class vs class (if multiple)
- Month vs month
- Category performance
- Platform performance

**Use Case:**
- Data-driven decisions
- Identify trends
- Optimize teaching
- Report to school

---

### 6. Communication Center
**Path:** `/guru/messages`

**Fitur:**

#### A. Student Communication
**Features:**
- Individual messaging
- Group messaging
- Broadcast announcements
- File sharing
- Read receipts

**Use Cases:**
- Daily guidance
- Quick questions
- Feedback delivery
- Motivation
- Announcements

#### B. Parent Communication
**Features:**
- Progress reports
- Performance updates
- Earnings reports
- Issue notifications
- Success celebrations

**Use Cases:**
- Keep parents informed
- Build trust
- Celebrate success
- Address concerns

#### C. Client Communication (if needed)
**Features:**
- Handle escalations
- Quality assurance
- Mediation
- Professional support

**Use Cases:**
- Resolve issues
- Maintain quality
- Build relationships
- Protect students

---

### 7. ML Training Data Management
**Path:** `/guru/ml-training`

**Fitur:**

#### A. Caption Rating
**Process:**
1. View AI-generated captions
2. Rate quality (1-5 stars)
3. Provide feedback
4. Tag categories
5. Mark for training

**Rating Criteria:**
- Grammar & spelling
- Tone appropriateness
- Creativity
- Engagement potential
- Platform optimization
- Brief alignment

#### B. Training Data Collection
**Collect:**
- High-quality captions
- Client feedback
- Performance data
- Success patterns
- Failure patterns

**Use Case:**
- Improve AI model
- Better output quality
- Reduce revision rate
- Increase satisfaction

#### C. Model Performance
**Monitor:**
- AI accuracy
- Client satisfaction
- Revision rate
- Success rate
- Improvement trends

**Use Case:**
- Track AI improvement
- Identify issues
- Optimize training
- Quality assurance

---

### 8. Reports & Documentation
**Path:** `/guru/reports`

**Fitur:**

#### A. Student Reports
**Generate Reports:**
- Individual performance
- Class performance
- Progress reports
- Skill assessments
- Earnings reports

**Export Formats:**
- PDF
- Excel
- CSV

#### B. School Reports
**For Administration:**
- Program overview
- Revenue generated
- Student participation
- Success metrics
- Impact assessment

#### C. Parent Reports
**For Parents:**
- Student progress
- Skills developed
- Earnings summary
- Achievements
- Areas for improvement

**Use Case:**
- Documentation
- Accountability
- Transparency
- Decision making

---

### 9. Settings & Configuration
**Path:** `/guru/settings`

**Fitur:**

#### A. Class Settings
- Class name
- Student capacity
- Operating hours
- Order acceptance rules
- Quality standards
- Pricing guidelines

#### B. Approval Settings
**Configure:**
- Auto-approve orders (yes/no)
- Quality check required (yes/no)
- Minimum rating threshold
- Maximum active orders per student
- Deadline buffer

#### C. Notification Settings
**Configure Alerts:**
- Order alerts
- Quality issues
- Deadline warnings
- Student performance
- Parent updates

**Use Case:**
- Customize workflow
- Quality control
- Risk management
- Efficiency

---

## 📊 Key Responsibilities:

### 1. Student Supervision
- Monitor daily activities
- Track order progress
- Ensure quality
- Provide guidance
- Intervene when needed

### 2. Quality Assurance
- Review student work
- Check before submission
- Maintain standards
- Handle escalations
- Protect reputation

### 3. Skill Development
- Create training materials
- Conduct workshops
- Provide feedback
- Track progress
- Certify skills

### 4. Performance Management
- Set targets
- Monitor metrics
- Recognize achievements
- Address issues
- Motivate students

### 5. Communication
- Daily check-ins
- Regular feedback
- Parent updates
- Client liaison
- Team coordination

### 6. Administration
- Generate reports
- Document progress
- Maintain records
- Report to school
- Compliance

---

## 🎓 Teaching Integration:

### Curriculum Alignment:

#### TKJ (Teknik Komputer & Jaringan):
- Digital marketing
- Content management
- Platform optimization
- Analytics
- Technical writing

#### Bahasa Indonesia:
- Creative writing
- Grammar & composition
- Persuasive writing
- Audience analysis
- Communication skills

### Learning Outcomes:
- Practical skills
- Real-world experience
- Professional ethics
- Client service
- Time management
- Financial literacy

### Assessment:
- Portfolio-based
- Performance metrics
- Client feedback
- Peer review
- Self-assessment

---

## 💰 Revenue Sharing (School):

### Distribution:
- **Total Revenue:** 100%
- **Platform Fee:** 20%
- **Student Earnings:** 80% of (100% - 20%) = 64%
- **School Fund:** 40% of platform fee = 8%
- **Teacher Incentive:** 30% of platform fee = 6%
- **Maintenance:** 30% of platform fee = 6%

### Example:
- Order value: Rp 100.000
- Platform fee: Rp 20.000 (20%)
- Student gets: Rp 80.000 (80%)
- School fund: Rp 8.000 (8%)
- Teacher incentive: Rp 6.000 (6%)
- Maintenance: Rp 6.000 (6%)

### Teacher Incentive:
- Based on class performance
- Monthly payout
- Bonus for top class
- Recognition awards

---

## 📈 Success Metrics:

### For Students:
- Orders completed
- Average rating
- Earnings
- Skill progression
- Client retention

### For Class:
- Total orders
- Completion rate
- Average rating
- Total revenue
- Student satisfaction

### For Teacher:
- Student success rate
- Quality maintenance
- Parent satisfaction
- School reputation
- Program growth

---

## 🛠️ Tools & Resources:

### Teaching Tools:
- Training materials
- Video tutorials
- Templates
- Case studies
- Assessment tools

### Monitoring Tools:
- Dashboard analytics
- Performance reports
- Quality checklist
- Alert system
- Communication platform

### Support Resources:
- Teacher guide
- Best practices
- Troubleshooting
- Community forum
- Technical support

---

## 💡 Best Practices untuk Guru:

### 1. Daily Routine:
- Check dashboard (morning)
- Review active orders
- Monitor deadlines
- Respond to messages
- Provide feedback
- Update reports (evening)

### 2. Weekly Tasks:
- Performance review
- Training sessions
- One-on-one meetings
- Quality audits
- Parent updates
- Planning next week

### 3. Monthly Tasks:
- Generate reports
- Evaluate progress
- Recognize achievements
- Address issues
- Update curriculum
- School presentation

### 4. Quality Assurance:
- Random spot checks
- Review before submission
- Client feedback analysis
- Continuous improvement
- Standard maintenance

### 5. Student Development:
- Identify strengths
- Address weaknesses
- Personalized guidance
- Skill progression
- Career counseling

### 6. Communication:
- Be available
- Respond quickly
- Give constructive feedback
- Celebrate success
- Support struggles

---

## 🎯 Goals & Objectives:

### Short-term (1-3 months):
- Onboard students
- Complete training
- First orders
- Build confidence
- Establish routine

### Mid-term (3-6 months):
- Consistent performance
- Quality improvement
- Client satisfaction
- Earnings growth
- Skill development

### Long-term (6-12 months):
- Expert operators
- Strong portfolio
- High ratings
- Sustainable income
- Career readiness

---

## 📞 Support & Resources:

### For Teachers:
- Teacher training program
- Mentorship from experienced teachers
- Community forum
- Technical support
- Regular updates

### Documentation:
- Teacher handbook
- Training materials
- Assessment tools
- Report templates
- Best practices guide

---

**Status:** ✅ Production Ready  
**Target:** Guru TKJ & Bahasa Indonesia  
**Goal:** Student success + Quality assurance  
**Support:** Full training & ongoing support available
