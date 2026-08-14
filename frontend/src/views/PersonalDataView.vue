<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h2>A00 個人資料維護</h2>
        <p class="page-subtitle">維護個人資料、語言能力、證照與工作經歷。</p>
      </div>
      <RouterLink v-if="isReadonly" to="/staff" class="back-link">← 返回人員管理</RouterLink>
    </div>

    <div class="section-card hero-card">
      <div class="photo-section">
        <img :src="photoPreview || form.photo_url || '/default-avatar.svg'" class="avatar" alt="個人照片" />
        <div class="hero-actions">
          <div class="hero-meta">
            <div><strong>{{ form.name || '未命名員工' }}</strong></div>
            <div class="meta-line">員工編號：{{ form.staff_no || '—' }}</div>
            <div class="meta-line">到職日期：{{ form.join_date || '—' }}</div>
            <div class="meta-line">組織：{{ regionName }} / {{ departmentName }} / {{ titleName }}</div>
          </div>
          <div class="action-row">
            <a :href="form.handbook_url || '/staff.pdf'" class="secondary-btn" target="_blank" rel="noopener noreferrer">閱讀員工守則</a>
            <template v-if="!isReadonly">
              <input ref="fileInput" type="file" accept="image/*" class="hidden-input" @change="onPhotoChange" />
              <button type="button" class="secondary-btn" @click="fileInput?.click()">更換照片</button>
              <button v-if="photoFile" type="button" @click="uploadPhoto">上傳</button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="save">
      <section class="section-card">
        <div class="section-heading">
          <h3>基本資料</h3>
          <span class="section-note">本人可維護，F02 帶入時唯讀。</span>
        </div>
        <div class="form-grid">
          <div class="form-group"><label>姓名</label><input v-model="form.name" :readonly="isReadonly" placeholder="姓名" /></div>
          <div class="form-group"><label>電話</label><input v-model="form.phone" :readonly="isReadonly" placeholder="電話" /></div>
          <div class="form-group">
            <label>性別</label>
            <select v-model="form.gender" :disabled="isReadonly">
              <option value="">選擇</option>
              <option value="M">男</option>
              <option value="F">女</option>
              <option value="OTHER">其他</option>
            </select>
          </div>
          <div class="form-group">
            <label>血型</label>
            <select v-model="form.blood_type" :disabled="isReadonly">
              <option value="">選擇</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="AB">AB</option>
              <option value="O">O</option>
            </select>
          </div>
          <div class="form-group"><label>生日</label><input type="date" v-model="form.birth_date" :disabled="isReadonly" /></div>
          <div class="form-group info"><label>狀態</label><span>{{ form.status || '—' }}</span></div>
          <div class="form-group info"><label>員工編號</label><span>{{ form.staff_no || '—' }}</span></div>
          <div class="form-group info"><label>所屬區域</label><span>{{ regionName }}</span></div>
          <div class="form-group info"><label>部門</label><span>{{ departmentName }}</span></div>
          <div class="form-group info"><label>職稱</label><span>{{ titleName }}</span></div>
        </div>
      </section>

      <section class="section-card">
        <div class="section-heading">
          <h3>地址資料</h3>
          <div v-if="!isReadonly" class="action-row compact-row">
            <label class="checkbox-inline">
              <input type="checkbox" v-model="form.mailing_address.same_as_registered" @change="toggleSameAddress" />
              同上
            </label>
            <button type="button" class="secondary-btn" @click="copyRegisteredToMailing">同上複製到通訊地址</button>
          </div>
        </div>
        <div class="address-grid">
          <fieldset class="address-card">
            <legend>戶籍地址</legend>
            <div class="form-grid compact-grid">
              <div class="form-group"><label>郵遞區號</label><input v-model="form.registered_address.postal_code" :readonly="isReadonly" /></div>
              <div class="form-group"><label>縣市</label><input v-model="form.registered_address.city" :readonly="isReadonly" /></div>
              <div class="form-group"><label>鄉鎮區</label><input v-model="form.registered_address.district" :readonly="isReadonly" /></div>
              <div class="form-group full-width"><label>詳細地址</label><input v-model="form.registered_address.address_line" :readonly="isReadonly" /></div>
            </div>
          </fieldset>
          <fieldset class="address-card">
            <legend>通訊地址</legend>
            <div class="form-grid compact-grid">
              <div class="form-group"><label>郵遞區號</label><input v-model="form.mailing_address.postal_code" :readonly="isReadonly || form.mailing_address.same_as_registered" /></div>
              <div class="form-group"><label>縣市</label><input v-model="form.mailing_address.city" :readonly="isReadonly || form.mailing_address.same_as_registered" /></div>
              <div class="form-group"><label>鄉鎮區</label><input v-model="form.mailing_address.district" :readonly="isReadonly || form.mailing_address.same_as_registered" /></div>
              <div class="form-group full-width"><label>詳細地址</label><input v-model="form.mailing_address.address_line" :readonly="isReadonly || form.mailing_address.same_as_registered" /></div>
            </div>
          </fieldset>
        </div>
      </section>

      <section class="section-card">
        <div class="section-heading">
          <h3>語言能力</h3>
          <button v-if="!isReadonly" type="button" class="secondary-btn" @click="addOtherLanguage">新增 Language Skills</button>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>英語能力</label>
            <select v-model="form.language_abilities.english" :disabled="isReadonly">
              <option value="">未填寫</option>
              <option v-for="option in proficiencyOptions" :key="`en-${option}`" :value="option">{{ option }}</option>
            </select>
          </div>
          <div class="form-group">
            <label>日語能力</label>
            <select v-model="form.language_abilities.japanese" :disabled="isReadonly">
              <option value="">未填寫</option>
              <option v-for="option in proficiencyOptions" :key="`jp-${option}`" :value="option">{{ option }}</option>
            </select>
          </div>
        </div>
        <table class="section-table">
          <thead>
            <tr><th>語言</th><th>口說</th><th>閱讀</th><th>書寫</th><th>備註</th><th v-if="!isReadonly">操作</th></tr>
          </thead>
          <tbody>
            <tr v-if="!form.language_abilities.other_languages.length">
              <td :colspan="isReadonly ? 5 : 6" class="empty-row">尚未填寫其他語言能力</td>
            </tr>
            <tr v-for="(row, index) in form.language_abilities.other_languages" :key="`language-${index}`">
              <td><input v-model="row.language" :readonly="isReadonly" placeholder="例如：台語" /></td>
              <td><input v-model="row.speaking" :readonly="isReadonly" placeholder="例如：流利" /></td>
              <td><input v-model="row.reading" :readonly="isReadonly" placeholder="例如：普通" /></td>
              <td><input v-model="row.writing" :readonly="isReadonly" placeholder="例如：普通" /></td>
              <td><input v-model="row.notes" :readonly="isReadonly" placeholder="備註" /></td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeOtherLanguage(index)">移除</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="section-card">
        <div class="section-heading"><h3>技能</h3><span class="section-note">以逗號分隔多個技能。</span></div>
        <div class="form-grid">
          <div class="form-group"><label>一般軟體</label><textarea v-model="skillText.officeSoftware" :readonly="isReadonly" rows="3" placeholder="Excel, Word, PowerPoint" /></div>
          <div class="form-group"><label>程式語言</label><textarea v-model="skillText.programmingLanguages" :readonly="isReadonly" rows="3" placeholder="PHP, JavaScript" /></div>
          <div class="form-group full-width"><label>專業技能</label><textarea v-model="skillText.professionalSkills" :readonly="isReadonly" rows="3" placeholder="招生、排課、報表分析" /></div>
          <div class="form-group full-width"><label>自評能力</label><textarea v-model="form.skills.self_evaluation" :readonly="isReadonly" rows="4" placeholder="請描述個人優勢與擅長領域" /></div>
        </div>
      </section>

      <section class="section-card">
        <div class="section-heading">
          <h3>證照 / 檢定</h3>
          <div v-if="!isReadonly" class="action-row compact-row">
            <button type="button" class="secondary-btn" @click="addEnglishCertification">新增英文檢定</button>
            <button type="button" class="secondary-btn" @click="addJapaneseCertification">新增日文檢定</button>
            <button type="button" class="secondary-btn" @click="addProfessionalCertification">新增專業證照</button>
          </div>
        </div>

        <h4>英文檢定</h4>
        <table class="section-table">
          <thead><tr><th>名稱</th><th>成績</th><th>發照單位</th><th>取得日期</th><th>備註</th><th v-if="!isReadonly">操作</th></tr></thead>
          <tbody>
            <tr v-if="!form.certifications.english.length"><td :colspan="isReadonly ? 5 : 6" class="empty-row">尚未填寫英文檢定</td></tr>
            <tr v-for="(row, index) in form.certifications.english" :key="`eng-cert-${index}`">
              <td><input v-model="row.name" :readonly="isReadonly" /></td>
              <td><input v-model="row.score" :readonly="isReadonly" /></td>
              <td><input v-model="row.issued_by" :readonly="isReadonly" /></td>
              <td><input type="date" v-model="row.acquired_on" :disabled="isReadonly" /></td>
              <td><input v-model="row.notes" :readonly="isReadonly" /></td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeEnglishCertification(index)">移除</button></td>
            </tr>
          </tbody>
        </table>

        <h4>日文檢定</h4>
        <table class="section-table">
          <thead><tr><th>名稱</th><th>成績</th><th>發照單位</th><th>取得日期</th><th>備註</th><th v-if="!isReadonly">操作</th></tr></thead>
          <tbody>
            <tr v-if="!form.certifications.japanese.length"><td :colspan="isReadonly ? 5 : 6" class="empty-row">尚未填寫日文檢定</td></tr>
            <tr v-for="(row, index) in form.certifications.japanese" :key="`jp-cert-${index}`">
              <td><input v-model="row.name" :readonly="isReadonly" /></td>
              <td><input v-model="row.score" :readonly="isReadonly" /></td>
              <td><input v-model="row.issued_by" :readonly="isReadonly" /></td>
              <td><input type="date" v-model="row.acquired_on" :disabled="isReadonly" /></td>
              <td><input v-model="row.notes" :readonly="isReadonly" /></td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeJapaneseCertification(index)">移除</button></td>
            </tr>
          </tbody>
        </table>

        <h4>其他專業證照</h4>
        <table class="section-table">
          <thead><tr><th>名稱</th><th>證號</th><th>發照單位</th><th>取得日期</th><th>到期日</th><th>備註</th><th v-if="!isReadonly">操作</th></tr></thead>
          <tbody>
            <tr v-if="!form.certifications.professional.length"><td :colspan="isReadonly ? 6 : 7" class="empty-row">尚未填寫專業證照</td></tr>
            <tr v-for="(row, index) in form.certifications.professional" :key="`professional-cert-${index}`">
              <td><input v-model="row.name" :readonly="isReadonly" /></td>
              <td><input v-model="row.license_no" :readonly="isReadonly" /></td>
              <td><input v-model="row.issued_by" :readonly="isReadonly" /></td>
              <td><input type="date" v-model="row.acquired_on" :disabled="isReadonly" /></td>
              <td><input type="date" v-model="row.expires_on" :disabled="isReadonly" /></td>
              <td><input v-model="row.notes" :readonly="isReadonly" /></td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeProfessionalCertification(index)">移除</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="section-card">
        <div class="section-heading">
          <h3>家庭狀況</h3>
          <button v-if="!isReadonly" type="button" class="secondary-btn" @click="addFamilyMember">新增家庭成員</button>
        </div>
        <table class="section-table">
          <thead><tr><th>姓名</th><th>關係</th><th>職業</th><th>聯絡方式</th><th>緊急聯絡人</th><th v-if="!isReadonly">操作</th></tr></thead>
          <tbody>
            <tr v-if="!form.family_information.length"><td :colspan="isReadonly ? 5 : 6" class="empty-row">尚未填寫家庭成員</td></tr>
            <tr v-for="(row, index) in form.family_information" :key="`family-${index}`">
              <td><input v-model="row.name" :readonly="isReadonly" /></td>
              <td><input v-model="row.relationship" :readonly="isReadonly" /></td>
              <td><input v-model="row.occupation" :readonly="isReadonly" /></td>
              <td><input v-model="row.phone" :readonly="isReadonly" /></td>
              <td>
                <label class="checkbox-inline">
                  <input type="checkbox" v-model="row.is_emergency_contact" :disabled="isReadonly" />
                  是
                </label>
              </td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeFamilyMember(index)">移除</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="section-card">
        <div class="section-heading">
          <h3>工作經歷</h3>
          <button v-if="!isReadonly" type="button" class="secondary-btn" @click="addWorkExperience">新增工作經歷</button>
        </div>
        <table class="section-table">
          <thead><tr><th>公司</th><th>職稱</th><th>開始日期</th><th>結束日期</th><th>工作內容</th><th v-if="!isReadonly">操作</th></tr></thead>
          <tbody>
            <tr v-if="!form.work_experiences.length"><td :colspan="isReadonly ? 5 : 6" class="empty-row">尚未填寫工作經歷</td></tr>
            <tr v-for="(row, index) in form.work_experiences" :key="`work-${index}`">
              <td><input v-model="row.company_name" :readonly="isReadonly" /></td>
              <td><input v-model="row.position" :readonly="isReadonly" /></td>
              <td><input type="date" v-model="row.start_date" :disabled="isReadonly" /></td>
              <td><input type="date" v-model="row.end_date" :disabled="isReadonly" /></td>
              <td><textarea v-model="row.responsibilities" :readonly="isReadonly" rows="2" /></td>
              <td v-if="!isReadonly"><button type="button" class="danger-btn" @click="removeWorkExperience(index)">移除</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <div v-if="!isReadonly" class="footer-actions">
        <button type="submit">儲存</button>
      </div>
      <p v-if="msg" class="success-msg">{{ msg }}</p>
      <p v-if="error" class="error-msg">{{ error }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { personalDataApi } from '@/api/personalData'

type NullableString = string | null

type Address = {
  postal_code: string
  city: string
  district: string
  address_line: string
  same_as_registered?: boolean
}

type LanguageRow = {
  language: string
  speaking: string
  reading: string
  writing: string
  notes: string
}

type Skills = {
  office_software: string[]
  programming_languages: string[]
  professional_skills: string[]
  self_evaluation: string
}

type CertificationEntry = {
  name: string
  score: string
  issued_by: string
  acquired_on: string
  notes: string
}

type ProfessionalCertificationEntry = {
  name: string
  license_no: string
  issued_by: string
  acquired_on: string
  expires_on: string
  notes: string
}

type FamilyMember = {
  name: string
  relationship: string
  occupation: string
  phone: string
  is_emergency_contact: boolean
}

type WorkExperience = {
  company_name: string
  position: string
  start_date: string
  end_date: string
  responsibilities: string
}

type PersonalDataForm = {
  id?: number
  staff_no: string
  name: string
  phone: string
  gender: string
  blood_type: string
  birth_date: string
  join_date: string
  photo_url: string
  handbook_url: string
  status: string
  region: { id?: number; name?: string } | null
  department: { id?: number; name?: string } | null
  title: { id?: number; name?: string } | null
  registered_address: Address
  mailing_address: Address
  language_abilities: {
    english: string
    japanese: string
    other_languages: LanguageRow[]
  }
  skills: Skills
  certifications: {
    english: CertificationEntry[]
    japanese: CertificationEntry[]
    professional: ProfessionalCertificationEntry[]
  }
  family_information: FamilyMember[]
  work_experiences: WorkExperience[]
}

const proficiencyOptions = ['基礎', '初級', '中級', '中高級', '進階', '流利']
const route = useRoute()
const isReadonly = computed(() => route.name === 'staff-personal-data-readonly')
const staffId = computed(() => route.params.id as string | undefined)
const fileInput = ref<HTMLInputElement | null>(null)
const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)
const msg = ref('')
const error = ref('')
const form = ref<PersonalDataForm>(createDefaultForm())
const skillText = ref({
  officeSoftware: '',
  programmingLanguages: '',
  professionalSkills: '',
})

const regionName = computed(() => form.value.region?.name || '—')
const departmentName = computed(() => form.value.department?.name || '—')
const titleName = computed(() => form.value.title?.name || '—')

function createDefaultForm(): PersonalDataForm {
  return {
    staff_no: '',
    name: '',
    phone: '',
    gender: '',
    blood_type: '',
    birth_date: '',
    join_date: '',
    photo_url: '',
    handbook_url: '/staff.pdf',
    status: '',
    region: null,
    department: null,
    title: null,
    registered_address: { postal_code: '', city: '', district: '', address_line: '' },
    mailing_address: { postal_code: '', city: '', district: '', address_line: '', same_as_registered: false },
    language_abilities: { english: '', japanese: '', other_languages: [] },
    skills: { office_software: [], programming_languages: [], professional_skills: [], self_evaluation: '' },
    certifications: { english: [], japanese: [], professional: [] },
    family_information: [],
    work_experiences: [],
  }
}

function normalizeText(value: unknown): string {
  return typeof value === 'string' ? value : value == null ? '' : String(value)
}

function normalizeAddress(address: Partial<Address> | null | undefined, includeSame = false): Address {
  return {
    postal_code: normalizeText(address?.postal_code),
    city: normalizeText(address?.city),
    district: normalizeText(address?.district),
    address_line: normalizeText(address?.address_line),
    ...(includeSame ? { same_as_registered: Boolean(address?.same_as_registered) } : {}),
  }
}

function normalizeIncomingData(data: Record<string, unknown> | undefined): PersonalDataForm {
  const base = createDefaultForm()
  const payload = data ?? {}

  return {
    ...base,
    id: typeof payload.id === 'number' ? payload.id : undefined,
    staff_no: normalizeText(payload.staff_no),
    name: normalizeText(payload.name),
    phone: normalizeText(payload.phone),
    gender: normalizeText(payload.gender),
    blood_type: normalizeText(payload.blood_type),
    birth_date: normalizeText(payload.birth_date),
    join_date: normalizeText(payload.join_date),
    photo_url: normalizeText(payload.photo_url),
    handbook_url: normalizeText(payload.handbook_url) || '/staff.pdf',
    status: normalizeText(payload.status),
    region: (payload.region as PersonalDataForm['region']) ?? null,
    department: (payload.department as PersonalDataForm['department']) ?? null,
    title: (payload.title as PersonalDataForm['title']) ?? null,
    registered_address: normalizeAddress(payload.registered_address as Partial<Address> | undefined),
    mailing_address: normalizeAddress(payload.mailing_address as Partial<Address> | undefined, true),
    language_abilities: {
      english: normalizeText((payload.language_abilities as { english?: NullableString } | undefined)?.english),
      japanese: normalizeText((payload.language_abilities as { japanese?: NullableString } | undefined)?.japanese),
      other_languages: Array.isArray((payload.language_abilities as { other_languages?: LanguageRow[] } | undefined)?.other_languages)
        ? ((payload.language_abilities as { other_languages?: LanguageRow[] }).other_languages ?? []).map((row) => ({
            language: normalizeText(row.language),
            speaking: normalizeText(row.speaking),
            reading: normalizeText(row.reading),
            writing: normalizeText(row.writing),
            notes: normalizeText(row.notes),
          }))
        : [],
    },
    skills: {
      office_software: Array.isArray((payload.skills as { office_software?: string[] } | undefined)?.office_software) ? ((payload.skills as { office_software?: string[] }).office_software ?? []).map(normalizeText).filter(Boolean) : [],
      programming_languages: Array.isArray((payload.skills as { programming_languages?: string[] } | undefined)?.programming_languages) ? ((payload.skills as { programming_languages?: string[] }).programming_languages ?? []).map(normalizeText).filter(Boolean) : [],
      professional_skills: Array.isArray((payload.skills as { professional_skills?: string[] } | undefined)?.professional_skills) ? ((payload.skills as { professional_skills?: string[] }).professional_skills ?? []).map(normalizeText).filter(Boolean) : [],
      self_evaluation: normalizeText((payload.skills as { self_evaluation?: NullableString } | undefined)?.self_evaluation),
    },
    certifications: {
      english: Array.isArray((payload.certifications as { english?: CertificationEntry[] } | undefined)?.english) ? ((payload.certifications as { english?: CertificationEntry[] }).english ?? []).map((row) => ({ name: normalizeText(row.name), score: normalizeText(row.score), issued_by: normalizeText(row.issued_by), acquired_on: normalizeText(row.acquired_on), notes: normalizeText(row.notes) })) : [],
      japanese: Array.isArray((payload.certifications as { japanese?: CertificationEntry[] } | undefined)?.japanese) ? ((payload.certifications as { japanese?: CertificationEntry[] }).japanese ?? []).map((row) => ({ name: normalizeText(row.name), score: normalizeText(row.score), issued_by: normalizeText(row.issued_by), acquired_on: normalizeText(row.acquired_on), notes: normalizeText(row.notes) })) : [],
      professional: Array.isArray((payload.certifications as { professional?: ProfessionalCertificationEntry[] } | undefined)?.professional) ? ((payload.certifications as { professional?: ProfessionalCertificationEntry[] }).professional ?? []).map((row) => ({ name: normalizeText(row.name), license_no: normalizeText(row.license_no), issued_by: normalizeText(row.issued_by), acquired_on: normalizeText(row.acquired_on), expires_on: normalizeText(row.expires_on), notes: normalizeText(row.notes) })) : [],
    },
    family_information: Array.isArray(payload.family_information) ? (payload.family_information as FamilyMember[]).map((row) => ({ name: normalizeText(row.name), relationship: normalizeText(row.relationship), occupation: normalizeText(row.occupation), phone: normalizeText(row.phone), is_emergency_contact: Boolean(row.is_emergency_contact) })) : [],
    work_experiences: Array.isArray(payload.work_experiences) ? (payload.work_experiences as WorkExperience[]).map((row) => ({ company_name: normalizeText(row.company_name), position: normalizeText(row.position), start_date: normalizeText(row.start_date), end_date: normalizeText(row.end_date), responsibilities: normalizeText(row.responsibilities) })) : [],
  }
}

function syncSkillText() {
  skillText.value = {
    officeSoftware: form.value.skills.office_software.join(', '),
    programmingLanguages: form.value.skills.programming_languages.join(', '),
    professionalSkills: form.value.skills.professional_skills.join(', '),
  }
}

function parseListInput(input: string): string[] {
  return input.split(',').map((item) => item.trim()).filter(Boolean)
}

function copyRegisteredToMailing() {
  form.value.mailing_address = {
    postal_code: form.value.registered_address.postal_code,
    city: form.value.registered_address.city,
    district: form.value.registered_address.district,
    address_line: form.value.registered_address.address_line,
    same_as_registered: true,
  }
}

function toggleSameAddress() {
  if (form.value.mailing_address.same_as_registered) {
    copyRegisteredToMailing()
  }
}

function addOtherLanguage() {
  form.value.language_abilities.other_languages.push({ language: '', speaking: '', reading: '', writing: '', notes: '' })
}
function removeOtherLanguage(index: number) {
  form.value.language_abilities.other_languages.splice(index, 1)
}
function addEnglishCertification() {
  form.value.certifications.english.push({ name: '', score: '', issued_by: '', acquired_on: '', notes: '' })
}
function removeEnglishCertification(index: number) {
  form.value.certifications.english.splice(index, 1)
}
function addJapaneseCertification() {
  form.value.certifications.japanese.push({ name: '', score: '', issued_by: '', acquired_on: '', notes: '' })
}
function removeJapaneseCertification(index: number) {
  form.value.certifications.japanese.splice(index, 1)
}
function addProfessionalCertification() {
  form.value.certifications.professional.push({ name: '', license_no: '', issued_by: '', acquired_on: '', expires_on: '', notes: '' })
}
function removeProfessionalCertification(index: number) {
  form.value.certifications.professional.splice(index, 1)
}
function addFamilyMember() {
  form.value.family_information.push({ name: '', relationship: '', occupation: '', phone: '', is_emergency_contact: false })
}
function removeFamilyMember(index: number) {
  form.value.family_information.splice(index, 1)
}
function addWorkExperience() {
  form.value.work_experiences.push({ company_name: '', position: '', start_date: '', end_date: '', responsibilities: '' })
}
function removeWorkExperience(index: number) {
  form.value.work_experiences.splice(index, 1)
}

function cleanupAddress(address: Address, includeSame = false) {
  return {
    postal_code: address.postal_code.trim(),
    city: address.city.trim(),
    district: address.district.trim(),
    address_line: address.address_line.trim(),
    ...(includeSame ? { same_as_registered: Boolean(address.same_as_registered) } : {}),
  }
}

function hasAnyValue(row: Record<string, unknown>, ignoredKeys: string[] = []) {
  return Object.entries(row).some(([key, value]) => {
    if (ignoredKeys.includes(key)) return false
    return typeof value === 'boolean' ? value : normalizeText(value).trim() !== ''
  })
}

function preparePayload() {
  const registered = cleanupAddress(form.value.registered_address)
  const mailing = form.value.mailing_address.same_as_registered ? { ...registered, same_as_registered: true } : cleanupAddress(form.value.mailing_address, true)

  return {
    name: form.value.name.trim(),
    phone: form.value.phone.trim(),
    gender: form.value.gender,
    blood_type: form.value.blood_type,
    birth_date: form.value.birth_date || null,
    registered_address: registered,
    mailing_address: mailing,
    language_abilities: {
      english: form.value.language_abilities.english || null,
      japanese: form.value.language_abilities.japanese || null,
      other_languages: form.value.language_abilities.other_languages
        .map((row) => ({
          language: row.language.trim(),
          speaking: row.speaking.trim(),
          reading: row.reading.trim(),
          writing: row.writing.trim(),
          notes: row.notes.trim(),
        }))
        .filter((row) => hasAnyValue(row)),
    },
    skills: {
      office_software: parseListInput(skillText.value.officeSoftware),
      programming_languages: parseListInput(skillText.value.programmingLanguages),
      professional_skills: parseListInput(skillText.value.professionalSkills),
      self_evaluation: form.value.skills.self_evaluation.trim(),
    },
    certifications: {
      english: form.value.certifications.english
        .map((row) => ({ name: row.name.trim(), score: row.score.trim(), issued_by: row.issued_by.trim(), acquired_on: row.acquired_on || null, notes: row.notes.trim() }))
        .filter((row) => hasAnyValue(row)),
      japanese: form.value.certifications.japanese
        .map((row) => ({ name: row.name.trim(), score: row.score.trim(), issued_by: row.issued_by.trim(), acquired_on: row.acquired_on || null, notes: row.notes.trim() }))
        .filter((row) => hasAnyValue(row)),
      professional: form.value.certifications.professional
        .map((row) => ({ name: row.name.trim(), license_no: row.license_no.trim(), issued_by: row.issued_by.trim(), acquired_on: row.acquired_on || null, expires_on: row.expires_on || null, notes: row.notes.trim() }))
        .filter((row) => hasAnyValue(row)),
    },
    family_information: form.value.family_information
      .map((row) => ({ name: row.name.trim(), relationship: row.relationship.trim(), occupation: row.occupation.trim(), phone: row.phone.trim(), is_emergency_contact: row.is_emergency_contact }))
      .filter((row) => hasAnyValue(row, ['is_emergency_contact'])),
    work_experiences: form.value.work_experiences
      .map((row) => ({ company_name: row.company_name.trim(), position: row.position.trim(), start_date: row.start_date || null, end_date: row.end_date || null, responsibilities: row.responsibilities.trim() }))
      .filter((row) => hasAnyValue(row)),
  }
}

async function load() {
  photoFile.value = null
  photoPreview.value = null
  msg.value = ''
  error.value = ''
  const response = isReadonly.value && staffId.value
    ? await personalDataApi.getByStaffId(staffId.value)
    : await personalDataApi.get()
  form.value = normalizeIncomingData(response.data?.data)
  syncSkillText()
}

async function save() {
  if (isReadonly.value) return
  try {
    form.value.skills.office_software = parseListInput(skillText.value.officeSoftware)
    form.value.skills.programming_languages = parseListInput(skillText.value.programmingLanguages)
    form.value.skills.professional_skills = parseListInput(skillText.value.professionalSkills)
    const response = await personalDataApi.update(preparePayload())
    form.value = normalizeIncomingData(response.data?.data)
    syncSkillText()
    msg.value = '儲存成功'
    error.value = ''
    setTimeout(() => (msg.value = ''), 3000)
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    const firstField = Object.values(err?.response?.data?.errors ?? {})[0]?.[0]
    error.value = firstField ?? err?.response?.data?.message ?? '儲存失敗'
    msg.value = ''
  }
}

function onPhotoChange(event: Event) {
  const target = event.target as HTMLInputElement
  photoFile.value = target.files?.[0] ?? null
  if (photoFile.value) {
    photoPreview.value = URL.createObjectURL(photoFile.value)
  }
  error.value = ''
}

async function uploadPhoto() {
  if (isReadonly.value || !photoFile.value) return
  try {
    const response = await personalDataApi.uploadPhoto(photoFile.value)
    form.value.photo_url = normalizeText(response.data?.data?.photo_url)
    photoFile.value = null
    photoPreview.value = null
    if (fileInput.value) fileInput.value.value = ''
    msg.value = '照片上傳成功'
    error.value = ''
    setTimeout(() => (msg.value = ''), 3000)
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    error.value = err?.response?.data?.errors?.photo?.[0] ?? err?.response?.data?.message ?? '照片上傳失敗'
    msg.value = ''
  }
}

onMounted(load)
watch(() => route.fullPath, load)
watch(
  () => ({ ...form.value.registered_address }),
  () => {
    if (form.value.mailing_address.same_as_registered) copyRegisteredToMailing()
  },
  { deep: true },
)
</script>

<style scoped>
.page { padding: 1rem; max-width: 1280px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1rem; }
.page-header h2 { margin: 0; }
.page-subtitle { margin: .35rem 0 0; color: #666; }
.back-link { color: #1890ff; text-decoration: none; margin-top: .25rem; }
.section-card { background: #fff; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; box-shadow: 0 6px 20px rgba(15, 23, 42, .05); }
.hero-card { padding: 1.5rem; }
.section-heading { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
.section-heading h3, h4 { margin: 0; }
.section-note { color: #666; font-size: .875rem; }
.photo-section { display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap; }
.avatar { width: 104px; height: 104px; border-radius: 50%; object-fit: cover; border: 2px solid #d9d9d9; background: #f5f5f5; }
.hero-actions { flex: 1; display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.hero-meta { min-width: 260px; }
.meta-line { margin-top: .35rem; color: #666; }
.action-row { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
.compact-row { margin-left: auto; }
.form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.compact-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.address-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.address-card { border: 1px solid #f0f0f0; border-radius: 10px; padding: 1rem; }
.address-card legend { padding: 0 .5rem; font-weight: 600; }
.form-group { display: flex; flex-direction: column; gap: .35rem; }
.form-group label { font-weight: 600; color: #333; }
.form-group.info span { min-height: 42px; display: flex; align-items: center; padding: .5rem .75rem; border: 1px solid #e5e7eb; border-radius: 8px; background: #fafafa; color: #555; }
.full-width { grid-column: 1 / -1; }
input, select, textarea { width: 100%; padding: .6rem .75rem; border: 1px solid #d9d9d9; border-radius: 8px; box-sizing: border-box; font: inherit; background: #fff; }
textarea { resize: vertical; }
input[readonly], select:disabled, textarea[readonly] { background: #fafafa; color: #666; cursor: not-allowed; }
button, .secondary-btn { display: inline-flex; align-items: center; justify-content: center; padding: .55rem 1rem; border: none; border-radius: 8px; background: #1890ff; color: #fff; cursor: pointer; text-decoration: none; font: inherit; }
.secondary-btn { background: #fff; color: #333; border: 1px solid #d9d9d9; }
.danger-btn { background: #ff4d4f; }
.section-table { width: 100%; border-collapse: collapse; margin-top: .75rem; }
.section-table th, .section-table td { border-bottom: 1px solid #f0f0f0; padding: .65rem; text-align: left; vertical-align: top; }
.section-table th { background: #fafafa; font-weight: 600; }
.section-table td input, .section-table td textarea { min-width: 110px; }
.checkbox-inline { display: inline-flex; align-items: center; gap: .35rem; color: #333; }
.checkbox-inline input { width: auto; }
.empty-row { color: #888; text-align: center; padding: 1rem; }
.footer-actions { display: flex; justify-content: flex-end; margin-top: 1rem; }
.success-msg { color: #389e0d; margin-top: .75rem; }
.error-msg { color: #cf1322; margin-top: .75rem; }
.hidden-input { display: none; }
@media (max-width: 960px) {
  .form-grid, .address-grid, .compact-grid { grid-template-columns: 1fr; }
  .hero-actions { flex-direction: column; }
  .section-table { display: block; overflow-x: auto; }
}
</style>
